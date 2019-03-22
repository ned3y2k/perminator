<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-15
 * Time: 오전 2:48
 */

namespace lib\api;

use classes\api\model\JSONResult;
use classes\runtime\serialization\json\IJsonUnserializable;
use classes\test\BitTestCase;

class TestJsonResult extends BitTestCase {
	public function testToString() {
		$class = new TestClass("Kyeongdae");
		$r = new JSONResult($class);
		$this->assertContains('Kyeongdae', (string)$r);

//		$ref = new \ReflectionClass('\lib\api\TestClass');
//		$ref->newInstanceWithoutConstructor();
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testDeserialize() {
		$class = new TestClass("Kyeongdae1");
		$r = new JSONResult($class);

		var_dump(JSONResult::fromJsonString((string)$r));
	}
}

class TestClass implements IJsonUnserializable {
	private $name;

	/**
	 * TestClass constructor.
	 *
	 * @param $name
	 */
	public function __construct(string $name) { $this->name = $name; }

	public static function a(){}

	function jsonUnserialize(\stdClass $stdClass = null) {
		$this->name = $stdClass->name;
	}
}