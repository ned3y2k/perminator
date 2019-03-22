<?php
namespace lib\db\query\mapper;

use classes\io\exception\IOException;
use classes\test\BitTestCase;
use classes\web\MultiPartFileResolver;

/**
 * Project: Inmoa.
 * User: Kyeongdae
 * Date: 2015-08-18
 * Time: 오후 5:41
 */

class TestMultiPartFileResolver extends BitTestCase {
	protected function getIgnoreCommonDbEnv() { return true; }

	protected function setUp() {
		$_SERVER[ 'REQUEST_METHOD' ] = 'POST';
		$_SERVER['CONTENT_TYPE'] = 'multipart/form-data';
	}

	/**
	 * @throws IOException
	 */
	public function testSingleFile() {
		$_FILES['test'] = array(
			'tmp_name'=>uniqid(),
			'name'=>'test.test',
			'size'=>1000,
			'error'=>1,
			'type'=>'test/bin'
		);
		$multipartFiles = MultiPartFileResolver::getInstance()->resolve();

		$this->assertFalse($multipartFiles['test']->isUploadSucceed());
	}

	/**
	 * @throws IOException
	 */
	public function testArrayFile() {
		$_FILES['test'] = array(
			'tmp_name'=>array(uniqid(), uniqid()),
			'name'=>array('test1.test', 'test2.test'),
			'size'=>array(1000, 1000),
			'error'=>array(1, 1),
			'type'=>array('test/bin', 'test/bin')
		);
		$multipartFiles = MultiPartFileResolver::getInstance()->resolve();

		$this->assertEquals(2, count($multipartFiles['test']));
		$this->assertFalse($multipartFiles['test'][0]->isUploadSucceed());
	}
}