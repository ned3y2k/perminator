<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-03-06
 * 시간: 오후 5:14
 */

namespace classes\test\selenium;
use app\classes\common\pool\AppEnvironmentPool;


/**
 * Class ServerExecutor
 *
 * @package classes\test\selenium
 */
class ServerExecutor {
	/** @var String */
	private $jrePath;
	/** @var string */
	private $jvmPath;
	/** @var string */
	private $seleniumServerJarPath;
	/** @var string */
	private $host;
	/** @var int */
	private $port;
	private $psexecPath;
	private $seleniumServerDriverPath;

	/**
	 * @param string $host
	 * @param int $post
	 * @param int $reTryLimit
	 * @param int $duration
	 * @return bool
	 * @throws \Exception
	 */
	public static function tryExecute($host, $post, $reTryLimit = 3, $duration = 3) {
		$runRequested = false;

		$tryCount = 0;
		while(!self::checkOpened($host, $post)) {
			if(!$runRequested) {
				$executor = new self($host, $post);
				$executor->execute();
				$runRequested = true;
			}

			if($tryCount++ > 3)
				return false;
			sleep($duration);
		}

		return true;
	}


	/**
	 * @param string $host
	 * @param int $port
	 * @return bool
	 */
	private static function checkOpened($host, $port) {
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		$fp = @fsockopen($host, $port);
		if ($fp) {
			fclose($fp);
			return true;
		}

		return false;
	}

	/**
	 * ServerExecutor constructor.
	 * @param $host
	 * @param $port
	 * @throws \Exception
	 */
	function __construct($host, $port) {
		$appEnv = AppEnvironmentPool::getInstance();

		$this->jrePath = $appEnv->__get('jrePath');
		if($this->isWindows()) {
			$this->jvmPath = $this->jrePath.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'java.exe';
		} else {
			$this->jvmPath = $this->jrePath.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'java';
		}
		$this->checkJre();

		$this->psexecPath = $appEnv->__get('psexecPath');
		$this->seleniumServerJarPath = $appEnv->__get('seleniumServerJarPath');
		$this->seleniumServerDriverPath = $appEnv->__get('seleniumServerDriverPath');

		$this->host = $host;
		$this->port = $port;
	}

	public function execute() {
		if(self::checkOpened($this->host, $this->port)) {
			// FIXME 백그라운드로 돌고 테스트가 가능하여야 한다.
			return ;
		}

		if(empty($this->seleniumServerJarPath) || !file_exists($this->seleniumServerJarPath))
			throw new ServerExecuteException("selenium jar path is empty or not found({$this->seleniumServerJarPath}). please set to ApplicationEnvironment.seleniumServerJarPath");
		if(empty($this->seleniumServerDriverPath) || !file_exists($this->seleniumServerDriverPath))
			throw new ServerExecuteException("selenium server Driver Path is empty or not found({$this->seleniumServerDriverPath}). please set to ApplicationEnvironment.seleniumServerDriverPath");

		$cmd = "\"{$this->jvmPath}\"".' -jar '.$this->seleniumServerJarPath.' '.$this->getDriverArgs();

		if($this->isWindows()) {
			if(empty($this->psexecPath) || !file_exists($this->psexecPath))
				throw new ServerExecuteException("selenium psexec Path is empty or not found({$this->psexecPath}). please set to ApplicationEnvironment.psexecPath. download to (https://technet.microsoft.com/ko-kr/sysinternals)");
			shell_exec($this->psexecPath.' -d '.$cmd);
		} else {
			// FIXME 백그라운드로 돌고 테스트가 가능하여야 한다.
			shell_exec($cmd.' &');
		}

	}

	private function checkJre() {
		if(empty($this->jrePath) || !file_exists($this->jrePath))
			throw new ServerExecuteException("jre path is empty or not found. ({$this->jrePath}). please set to ApplicationEnvironment.jrePath");
		if(empty($this->jvmPath) || !file_exists($this->jvmPath))
			throw new ServerExecuteException("jvm path is empty or not found. ({$this->jvmPath}). please set to ApplicationEnvironment.jvmPath");
	}

	/**
	 * @return bool
	 */
	private function isWindows() {
		return strpos(strtolower(php_uname('s')), 'windows') !== false;
	}

	/** @return string */
	private function getDriverArgs() {
		$dir = new \DirectoryIterator($this->seleniumServerDriverPath);

		$argList = array();
		foreach($dir as $driver) {
			if($driver->isDot() || !$driver->isFile())
				continue;

			$argList[] = 'webdriver.'.$this->createDriverKey($driver->getFilename()).'.driver='.$driver->getPathname();
		}

		return '-D'.implode(' -D', $argList);
	}

	/**
	 * @param string $fileName
	 * @return string
	 */
	private function createDriverKey($fileName) {
		$fileName = strtolower($fileName);
		$pos = strpos($fileName, 'driver');
		return substr($fileName, 0, $pos);
	}
}