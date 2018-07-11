<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-18
 * Time: 오전 5:20
 */

namespace lib\classes\runtime\seriallization\json;


use classes\runtime\serialization\json\IJsonUnserializable;
use classes\runtime\serialization\json\JSON;
use classes\runtime\serialization\json\JsonIgnoreField;
use classes\runtime\serialization\json\JsonSerializable;
use classes\test\BitTestCase;

class TestJson extends BitTestCase {

	/**
	 * @return string
	 * @throws \ReflectionException
	 */
	public function testJsonEncode() {
		$jsonString = JSON::encode(new TestClass());
		$this->assertNotContains('ignore', $jsonString);

		return $jsonString;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function testJsonDecode() {
		$instance = JSON::decode($this->testJsonEncode());
		$this->assertInstanceOf('lib\classes\runtime\seriallization\json\TestClass', $instance);
	}
}

class TestClass implements JsonSerializable, IJsonUnserializable {
	public $ignoreField     = "ignore";
	public $serializedField = "serialized";

	/**
	 * @param \stdClass $out
	 *
	 * @return void
	 */
	public function getSerializeConfig(\stdClass &$out) {
		$out->ignoreField = JsonIgnoreField::getInstance();
	}

	function jsonUnserialize(\stdClass $stdClass = null) {
		$this->serializedField = $stdClass->serializedField;
	}
}