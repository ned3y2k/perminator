<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-30
 * Time: 오전 7:37
 */

namespace lib\model;


use classes\model\Hexadecimal;
use classes\test\BitTestCase;


class TestHexadecimal extends BitTestCase {
	public function testUnsigned() {
		$i = Hexadecimal::fromString("ff0a0b");
		$this->assertEquals([-1, 10, 11], $i->getBytes());
	}

	public function testSigned() {
		$i = Hexadecimal::fromString("ff0a0b");
		$i->setUnsigned(true);
		$this->assertEquals([255, 10, 11], $i->getBytes());
	}
}
