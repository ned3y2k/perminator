<?php
use classes\model\board\Post;
require_once 'index.php';
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * test case.
 */
class CashManagerTest extends PHPUnit_Framework_TestCase {
	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		parent::tearDown ();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct() {}

	public function testPut() {
		$p = new Post();
		$this->assertEquals(1, $p->getId());
	}
}