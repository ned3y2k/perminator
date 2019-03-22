<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-01
 * 시간: 오후 11:03
 */

namespace model;

use classes\api\model\JSONResult;
use classes\test\BitTestCase;

class TestJSONModel extends BitTestCase {
	protected function setUp() {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);
	}

	/**
	 * @throws \ReflectionException
	 */
	function testToString() {
		$res = new JSONResult("1234");
		$string = $res->__toString();
		$this->assertNotEmpty($string);
		var_dump($string);
	}

	/**
	 * @throws \ReflectionException
	 */
	function testAppendAndToString() {
		$res = new JSONResult();
		$res->put('aaa','111');
		var_dump($res->__toString());

		$res->put('bbb','111');
		var_dump($res->__toString());
	}
}
