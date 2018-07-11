<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 13
 * 시간: 오후 7:30
 */
namespace classes\database\query\mapper\node;

use classes\database\conf\mapper\DynamicQueryConst;
use classes\database\query\mapper\exception\node\QueryNodeAttributeNotExistException;
use classes\database\query\mapper\node\attribute\QueryNodeAttributeTestConditional;

/**
 * test를 통과하면 자식 노드를 리턴
 * Class QueryNodeIf
 * @package classes\database\query\mapper\node
 */
class QueryNodeIf extends AbstractChildNode {
	/** @var QueryNodeAttributeTestConditional */
	private $testConditional;

	public function setAttributes(array $attributes) {
		if(!array_key_exists(DynamicQueryConst::ATTR_TEST, $attributes)) throw new QueryNodeAttributeNotExistException('test(conditional) statement not found');

		$this->testConditional = new QueryNodeAttributeTestConditional($attributes[DynamicQueryConst::ATTR_TEST]);
	}

	public function __toString() {
        if($this->context == null) throw new QueryNodeAttributeNotExistException('if node context was empty');

		if($this->testConditional->test($this->context)) {
			$buff = '';
			foreach($this->childNodes as $childNode) {
				$buff .= $childNode->__toString();
			}

			return $buff;
		}

		return '';
	}
}