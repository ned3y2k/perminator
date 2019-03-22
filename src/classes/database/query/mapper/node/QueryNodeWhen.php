<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-09-22
 * 시간: 오후 1:21
 */
namespace classes\database\query\mapper\node;

use classes\database\conf\mapper\DynamicQueryConst;
use classes\database\query\mapper\exception\node\QueryNodeAttributeNotExistException;
use classes\database\query\mapper\node\attribute\QueryNodeAttributeTestConditional;

class QueryNodeWhen extends AbstractChildNode {
	/** @var QueryNodeAttributeTestConditional */
	private $testConditional;

	public function setAttributes(array $attributes) {
		if(!array_key_exists(DynamicQueryConst::ATTR_TEST, $attributes)) throw new QueryNodeAttributeNotExistException('test(conditional) statement not found');

		$this->testConditional = new QueryNodeAttributeTestConditional($attributes[DynamicQueryConst::ATTR_TEST]);
	}

	public function __toString() {
		if($this->context == null) throw new QueryNodeAttributeNotExistException('choose node context was empty');

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