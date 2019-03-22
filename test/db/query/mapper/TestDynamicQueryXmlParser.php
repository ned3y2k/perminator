<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 12
 * 시간: 오후 11:01
 */
namespace lib\db\query\mapper;

use classes\database\query\mapper\parser\XmlParser;
use classes\test\BitTestCase;

class TestDynamicQueryXmlParser extends BitTestCase {
	const XML_PATH = __DIR__ . '/TestDynamicQueryXmlParser.xml';
	private $xml = self::XML_PATH;


	protected function setUp() {
		$this->xml = file_get_contents(self::XML_PATH);
	}

	public function test_getFunction() {
        $args = array('title'=>'book', 'title2'=>'a');

        $parser = new XmlParser($this->xml);
        echo $parser->getMapper()->getFunction('selectRows', $args);

	}

	public function testDynamic2() {
        //$parser = new DynamicQueryMapperXmlParser($this->xml);
        //echo $parser->getMapper()->getExecFun
        //ction('select');
	}
}