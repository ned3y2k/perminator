<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 13
 * 시간: 오후 7:28
 */
namespace classes\database\query\mapper\node;

use classes\database\conf\mapper\DynamicQueryConst;
use classes\database\query\mapper\exception\DynamicQueryBuilderException;
use classes\database\query\mapper\exception\node\QueryNodeFunctionException;

class QueryNodeProcedure extends AbstractChildNode {
	public $id;

	public function setAttributes(array $attributes) {
		if(!array_key_exists(DynamicQueryConst::ATTR_ID, $attributes)) {
			throw new QueryNodeFunctionException("id not found");
		}
		$this->id = $attributes[DynamicQueryConst::ATTR_ID];
	}

	public function setText($text) { throw new DynamicQueryBuilderException('invalid operation'); }
}