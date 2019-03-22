<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-03-01
 * 시간: 오전 1:36
 */

namespace lib;


use classes\test\BitTestCase;

class Test extends BitTestCase {
	function testPathNormalize() {

		echo normalizepath("d:\\inmoa\\..\\");
	}
}
