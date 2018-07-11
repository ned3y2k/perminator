<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 14
 * 시간: 오후 6:42
 */
namespace lib\db\query\mapper\node;

use classes\database\query\mapper\DynamicQueryContext;
use classes\database\query\mapper\node\QueryNodeIf;
use classes\database\query\mapper\node\QueryTextNode;
use classes\test\BitTestCase;

if(!defined('BITMOBILE')) define('BITMOBILE', null);
require_once getenv('PROJECT_LOCATION') . '/lib/init.php';

/**
 * Class TestQueryNodeIf
 * @package lib\db\query\mapper
 */
class TestQueryNodeIf extends BitTestCase {
	private static function createContext() {
		$arr = array('title'=>'Legend Of Heroes');
		return new DynamicQueryContext($arr);
	}

	public function test_ifNode() {
		$expected = 'successful';

		$node = new QueryNodeIf();
		$node->setContext(self::createContext());

		$childNode = new QueryTextNode();
		$childNode->setText($expected);

		$node->addNode($childNode);
		$node->setAttributes(array('test'=>"#title !== null"));

        $this->assertEquals($expected, $node->__toString());

		$this->assertEquals('if', $node->nodeName());
	}
}