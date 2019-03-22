<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-03
 * 시간: 오전 10:45
 */

namespace lang;

use classes\lang\StringBuilder;
use classes\test\BitTestCase;


class TestStringBuilder extends BitTestCase {
	public function test() {
		$sb = new StringBuilder();
		$sb->append("1234");

		$this->assertEquals('1234', $sb->toString());
	}
}