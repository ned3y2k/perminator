<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-03
 * 시간: 오전 10:49
 */

namespace lang;


use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\FileNotFoundException;
use classes\io\exception\PermissionException;
use classes\lang\FileStringBuilder;
use classes\test\BitTestCase;


class TestFileStringBuilder extends BitTestCase {
	/**
	 * @throws DirectoryNotFoundException
	 * @throws FileNotFoundException
	 * @throws PermissionException
	 */
	public function test() {
		$sb = new FileStringBuilder();
		$sb->append("1234");
		$sb->append("1234");
		$this->assertEquals('12341234', $sb->toString());
	}
}