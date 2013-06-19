<?php
use classes\cache\CacheManagerPool;
require_once 'perminator.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * test case.
 */
class CashManagerTest extends PHPUnit_Framework_TestCase {
	/**
	 * @var \classes\cache\CacheManager
	 */
	private $cacheManager;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
		$this->cacheManager = CacheManagerPool::getIntance(CacheManagerPool::Serializing);
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->cacheManager->clear();
		parent::tearDown ();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}

	public function testPut() {
		$this->cacheManager->put("test", "1234");
		self::assertNotNull($this->cacheManager->get("test"));
	}

	public function testEmptyGet() {
		self::assertNull($this->cacheManager->get("test1234"));
	}

	public function testDeleteGet() {
		$this->cacheManager->put("testD", "1234");
		$this->cacheManager->delete("testD");
		self::assertNull($this->cacheManager->get("testD"));
	}
}