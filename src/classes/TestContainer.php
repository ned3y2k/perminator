<?php
/**
 * User: Kyeongdae
 * Date: 2015-02-23
 * Time: 오전 8:06
 */

namespace classes;

if (!defined('TES_DB_INIT_LEVEL')) {
	/**
	 * TEST 시 DB 초기화 여부
	 * Profile 중에는 꺼야 할수 있다.(DB에 캐시를 넣기 떄문.)
	 * 3: 모든테이블 드롭 및 재설치
	 * 2: 모든테이블 비움 및 업그레이드
	 * 1: 모든 테이블 비움
	 * 0: 유지
	 */
	define('TES_DB_INIT_LEVEL', '1');
}

interface ITestContainerInitializer {
	function initialize();
}

/**
 * Class TestContainer
 * @package classes
 */
class TestContainer {
	/** @var array */
	protected $longOptions = array(
		'colors'             => null,
		'bootstrap='         => null,
		'configuration='     => null,
		'coverage-html='     => null,
		'coverage-clover='   => null,
		'coverage-php='      => null,
		'coverage-text=='    => null,
		'debug'              => null,
		'exclude-group='     => null,
		'filter='            => null,
		'testsuite='         => null,
		'group='             => null,
		'help'               => null,
		'include-path='      => null,
		'list-groups'        => null,
		'loader='            => null,
		'log-json='          => null,
		'log-junit='         => null,
		'log-tap='           => null,
		'process-isolation'  => null,
		'repeat='            => null,
		'stderr'             => null,
		'stop-on-error'      => null,
		'stop-on-failure'    => null,
		'stop-on-incomplete' => null,
		'stop-on-skipped'    => null,
		'strict'             => null,
		'tap'                => null,
		'testdox'            => null,
		'testdox-html='      => null,
		'testdox-text='      => null,
		'test-suffix='       => null,
		'no-configuration'   => null,
		'no-globals-backup'  => null,
		'printer='           => null,
		'static-backup'      => null,
		'verbose'            => null,
		'version'            => null
	);

	/** @var bool */
	private $initialized = false;

	/**
	 * @param string $className
	 * @param string $methodName
	 * @param ITestContainerInitializer[] $initializers
	 * @throws \ReflectionException
	 */
	public function init(string $className, string $methodName, array $initializers = array()) {
		if ($this->initialized) return;

		list($file, $line) = $this->findInvokeMeta($className, $methodName);

		echo "------------ invoke information ------------\n";
		echo "loaded script: {$file}:{$line}\n";
		echo "invoked method: {$className}.{$methodName}\n";
		echo "------------ invoked begin init test env ------------\n";

		$this->initGlobalVars();

		foreach ($initializers as $initializer) {
			$initializer->initialize();
		}


		$this->initialized = true;

		echo "------------ end init test env ------------\n\n";
	}

	/** DB 초기화가 필요시 */
	public function requireInitDB() { $this->initialized = false; }


	private function initGlobalVars() {
		echo "**** init global vars ****\n";

		if (!array_key_exists('REQUEST_URI', $_SERVER)) {
			echo "\$_SERVER['REQUEST_URI'] is not found. skip all interceptor and init empty string.\n";
			$_SERVER['REQUEST_URI'] = '';
		}

		if (!array_key_exists('REQUEST_METHOD', $_SERVER)) {
			echo "\$_SERVER['REQUEST_METHOD'] is not found. default value is GET.\n";
			$_SERVER['REQUEST_METHOD'] = 'GET';
		}

		echo "**** init global vars end ****\n\n";
	}


	/**
	 * @param string $className
	 * @param string $methodName
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
	private function findInvokeMeta($className, $methodName) {
		$ref = new \ReflectionClass($className);
		$ref = $ref->getMethod($methodName);

		return array($ref->getFileName(), $ref->getStartLine());
	}
}