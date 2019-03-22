<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-15
 * Time: 오후 12:26
 */

namespace lib\db\query\mapper;

use classes\database\IDatabaseStatement;
use classes\database\query\mapper\behavior\DynamicQueryMysqlBehavior;
use classes\database\query\mapper\behavior\IBehavior;
use classes\test\BitTestCase;

class TestDynamicQueryArrayForeach extends BitTestCase {
	const XML_PATH = __DIR__ . '/TestDynamicQueryArrayForeach.xml';

	/** @var DynamicQueryMysqlBehavior */
	private $behavior;
	private $xml;

	/**
	 * @throws \Exception
	 */
	protected function setUp() {
		$this->xml = file_get_contents(self::XML_PATH);

		$this->behavior = new DynamicQueryMysqlBehavior(
			DatabaseConnectionPool::getInstance(),
			'\stdClass',
			IDatabaseStatement::RESULT_OBJECT,
			null);
	}

	public function test() {
		$this->behavior->execFunction('select', $this->args(), $this->xml, IBehavior::TYPE_STRING);
	}


	/**
	 * @return array
	 */
	private function args(): array {
		return [
			'list' => [
				['var' => 1],
				['var' => 2],
				['var' => 3],
			]
		];
	}
}