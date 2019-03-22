<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-01
 * 시간: 오후 10:54
 */

namespace lang;

use classes\lang\StringPool;
use classes\lang\UniqueObject;
use classes\test\BitTestCase;

/**
 * Class TestStringPool
 *
 * @package lang
 */
class TestStringPool extends BitTestCase  {
	const TEST_STRING = 'testString';
	/** @var StringPool */
	private $pool;

	protected function setUp() {
		$this->pool = StringPool::getInstance();
	}

	function testPut() {
		$key = $this->pool->put(self::TEST_STRING);
		$this->assertNotEmpty($key);
	}

	function testFailPut() {
		$this->expectException('\InvalidArgumentException');
		$key = $this->pool->put(array());
	}

	function testGet() {
		$key = $this->pool->put(self::TEST_STRING);
		$this->assertEquals(self::TEST_STRING, $this->pool->get($key));
	}

	function testEmpty() {
		$key = $this->pool->put(self::TEST_STRING);
		$this->pool->remove($key);
		$this->assertEmpty($this->pool->get($key));
	}

	function testContainsKey() {
		$key = $this->pool->put(self::TEST_STRING, 1);
		$this->assertTrue($this->pool->containsKey($key));
	}

	function testContains() {
		$key = $this->pool->put(self::TEST_STRING, 1);
		$this->pool->key(self::TEST_STRING);
		$this->assertEquals(self::TEST_STRING, $this->pool->get($key));
	}

	function testArrayContains() {
		$this->pool->containsKey(1);
	}

	function testFailArrayContains() {
		$this->expectException('\InvalidArgumentException');
		$this->pool->containsKey(array());
	}

	function testContainsObjectKey() {
		$this->pool->containsKey(new TestStringPoolMock());
	}
}

class TestStringPoolMock extends UniqueObject {

}