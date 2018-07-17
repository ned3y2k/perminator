<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2015-02-27
 * Time: 오전 2:58
 */

namespace classes\test;

use classes\TestContainer;
use PHPUnit\Framework\TestCase;

define('BitTestCaseDefined', 1);

/**
 * TEST 시 DB 초기화 여부
 * Profile 중에는 꺼야 할수 있다.(DB에 캐시를 넣기 떄문.)
 * 3: 모든테이블 드롭 및 재설치
 * 2: 모든테이블 비움 및 업그레이드
 * 1: 모든 테이블 비움
 * 0: 유지
 */
//define('TES_DB_INIT_LEVEL', '1');
//define('TEST_DB_CONVERT_MEMORY_DB', 1);

/**
 * Class BitTestCase
 *
 * @package classes\test
 */
class BitTestCase extends TestCase {
	/** @var TestContainer */
	private $testContainer;

	/**
	 * @throws \Exception
	 * @throws \ReflectionException
	 */
	public function runBare(): void {
		$this->testContainer = new TestContainer();
		$this->testContainer->init(
			get_class($this)
			, $this->getName()
//			, [new DBInitializer($this->getLocalDBSqlPath(), $this->getIgnoreCommonDbEnv())]
		);

		parent::runBare();
	}

	protected function getIgnoreCommonDbEnv() { return false; }

	/** @return string|null */
	protected function getLocalDBSqlPath() {
		$root = _APP_ROOT_ . 'test/db/';
		$sqlList = array_slice(scandir($root), 2);
		foreach ($sqlList as &$sql) {
			$sql = $root . $sql;
		}

		return $sqlList;
	}
}