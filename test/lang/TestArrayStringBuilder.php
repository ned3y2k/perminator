<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-03
 * 시간: 오전 10:41
 */
namespace lang;

use classes\lang\ArrayStringBuilder;
use classes\test\BitTestCase;

class TestArrayStringBuilder extends BitTestCase  {
	public function test() {
		$a = new ArrayStringBuilder();
		$a->append("1234");
		$a->append("1234");
		$this->assertEquals('12341234', $a->toString());
	}
}