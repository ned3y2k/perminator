<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-11
 * Time: 오전 3:05
 */

namespace lib\db\query\mapper\node;

if(!defined('BITMOBILE')) define('BITMOBILE', null);
require_once getenv('PROJECT_LOCATION') . '/lib/init.php';

use classes\database\query\mapper\DynamicQueryContext;
use classes\database\query\mapper\node\QueryNodeForeach;
use classes\database\query\mapper\node\QueryNodeIf;
use classes\database\query\mapper\node\QueryNodeWhen;
use classes\database\query\mapper\node\QueryTextNode;
use classes\test\BitTestCase;

class TestQueryNodeForeach extends BitTestCase {
	private static function createContext() {
		$arr = [
			'testArray'=>[1,2,3,4]
		];
		return new DynamicQueryContext($arr);
	}


	public function testChildAdd() {
		$node = new QueryNodeForeach();
		$node->setContext(self::createContext());
		$node->addNode(new QueryNodeIf());
	}

	/** @test */
	public function failTestChildAdd() {
		$this->setExpectedException('classes\database\query\mapper\exception\node\QueryNodeChildRuleException');
		$node = new QueryNodeForeach();
		$node->setContext(self::createContext());
		$node->addNode(new QueryNodeWhen());
	}

	public function test1() {
		$node = new QueryNodeForeach();
		$node->setContext(self::createContext());
		$node->setAttributes([
			'collection'=>'#testArray',
			'separator'=>",",
			'item'=>'#item',
			'open'=>'(',
			'close'=>')',
		]);

		$textNode = new QueryTextNode();
		$textNode->setText("#{item}");

		$node->addNode($textNode);
		echo $node->__toString();
	}
}