<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-03-06
 * 시간: 오후 5:23
 */

namespace test\selenium;

use classes\test\BitTestCase;
use classes\test\selenium\ServerExecutor;

class TestServerExecutor extends BitTestCase {
	/**
	 * @throws \Exception
	 */
	public function test() {
		$executor = new ServerExecutor('localhost', 4444);
		$executor->execute();
	}
}