<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 14. 9. 15
 * 시간: 오후 2:30
 */

namespace lib\db\query\mapper\Behavior;

use app\classes\common\pool\MysqliPool;
use classes\database\query\mapper\behavior\DynamicQueryMysqlBehavior;
use classes\test\BitTestCase;

if (!defined('BITMOBILE')) define('BITMOBILE', null);
require_once getenv('PROJECT_LOCATION') . '/lib/init.php';

class TestDynamicQueryMysqlBehavior extends BitTestCase {
	/** @var DynamicQueryMysqlBehavior */
	private $behavior;
	/** @var string */
	private $xml;

	/**
	 * @throws \Exception
	 */
	protected function setUp() {
		parent::setUp(); // TODO: Change the autogenerated stub
		$this->xml = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'TestDynamicQueryMysqlBehavior'.'.xml');
		$connection = MysqliPool::getInstance();
		$this->behavior = new DynamicQueryMysqlBehavior($connection);
	}

	public function test() {
		$args = array('var1' => '10', 'var2' => 20);
		$expectedResult = ($args['var1'] + $args['var2']) * $args['var1'];

		$r = $this->behavior->execFunction('select', $args, $this->xml, DynamicQueryMysqlBehavior::TYPE_STRING);
		$this->assertEquals($expectedResult, $r[0]->r);
	}

	public function testSelectSlashed() {
		$args = array('var1' => "\\domain");
		$r = $this->behavior->execFunction('selectSlashes', $args, $this->xml, DynamicQueryMysqlBehavior::TYPE_STRING);
		$this->assertEquals("\\domain", $r[0]->r);
	}

	public function testSelectSlashes() {
		$args = array('var1' => "\\\\domain");
		$r = $this->behavior->execFunction('selectSlashes', $args, $this->xml, DynamicQueryMysqlBehavior::TYPE_STRING);
		$this->assertEquals("\\\\domain", $r[0]->r);
	}

	public function testSelectNull() {
		$args = array('var1' => null);
		$r = $this->behavior->execFunction('selectNull', $args, $this->xml, DynamicQueryMysqlBehavior::TYPE_STRING);
		$this->assertEquals(null, $r[0]->r);
	}
} 