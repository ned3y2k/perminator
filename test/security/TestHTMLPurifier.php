<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 2015-01-24
 * 시간: 오전 12:46
 */
use classes\test\BitTestCase;

if(!defined('BITMOBILE')) define('BITMOBILE', null);
require_once getenv('PROJECT_LOCATION') . '/lib/init.php';

class TestHTMLPurifier extends BitTestCase {
	public function test_purifier() {
		load_lib('ext/htmlpurifier-4.6.0/HTMLPurifier.includes');
		$purifier = new HTMLPurifier();
		$cleanHtml = $purifier->purify('<p>');
		var_dump($cleanHtml);
	}
}
