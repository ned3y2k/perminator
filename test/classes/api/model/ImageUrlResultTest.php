<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 4:43
 */

namespace classes\api\model;


use PHPUnit\Framework\TestCase;

class ImageUrlResultTest extends TestCase {
	public function testGetContentType() {
		$r = new ImageUrlResult("a.jpg");
		$this->assertEquals("image/jpeg", $r->getContentType());
	}


	public function testFailGetContentType() {
		$this->expectException("\UnsupportedOperationException");
		$r = new ImageUrlResult("a.jpssg");
	}
}
