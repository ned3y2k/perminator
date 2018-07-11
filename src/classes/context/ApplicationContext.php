<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:30
 */

namespace classes\context;


use classes\handler\throwable\IThrowableHandler;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\io\File;
use classes\lang\ArrayUtil;
use classes\util\ServerEnvironment;

class ApplicationContext implements IApplicationContext {
	/** @var SharedUserContext 사용자 공유 Context */
	private $sharedUserContext;
	/** @var ResponseContext */
	private $responseContext;
	/** @var RequestContext */
	private $requestContext;
	/** @var ServerEnvironment */
	private $environment;
	/** @var bool 디버그 여부 */
	private $debug = false;
	/** @var string[] */
	private $debugFlags;

	/** @var IThrowableHandler */
	private $exceptionHandler;

	public function __construct() {
		if (file_exists(_APP_ROOT_ . 'debug')) {
			$this->debug      = true;
			$this->debugFlags = parse_ini_file(_APP_ROOT_ . 'debug');
		}

		$this->responseContext   = new ResponseContext();
		$this->requestContext    = new RequestContext();
		$this->sharedUserContext = new SharedUserContext(new \DateTime());
		$this->environment       = new ServerEnvironment($this);
	}

	/** @return bool */
	public function isDebug() { return $this->debug; }

	public function getSharedUserContext(): SharedUserContext { return $this->sharedUserContext; }
	public function getRequestContext(): RequestContext { return $this->requestContext; }
	public function getResponseContext(): ResponseContext { return $this->responseContext; }
	public function getServerEnvironment(): ServerEnvironment { return $this->environment; }

	/**
	 * 디버그 정보 입력
	 *
	 * @param string       $fileName 파일경로
	 * @param object|mixed $object   기록할 내용
	 *
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function writeDebugInfo($fileName, $object) {
		File::appendAllText(_DIR_LOG_PHP_USR_ . $fileName, var_export($object, true), true);
	}

	/**
	 * 메서드 명 정규화
	 *
	 * @param $methodName
	 * @param $line
	 *
	 * @return string
	 */
	public function callNameNormalization($methodName, $line) {
		return str_replace('::', '-', str_replace('\\', '.', $methodName)) . "($line)";
	}

	public function getExceptionHandler(): IThrowableHandler { return $this->exceptionHandler; }

	/** @param IThrowableHandler $exceptionHandler */
	public function setExceptionHandler(IThrowableHandler $exceptionHandler) { $this->exceptionHandler = $exceptionHandler; }

	/**
	 * @param string|null $key
	 * @param string      $default 값이 지정되어 있지 않을때 기본 값
	 *
	 * @return array|string
	 */
	public function getDebugFlag($key = null, $default = null) {
		if ($key == null) return $this->debugFlags;

		return ArrayUtil::getValue($this->debugFlags, $key, $default);
	}
}