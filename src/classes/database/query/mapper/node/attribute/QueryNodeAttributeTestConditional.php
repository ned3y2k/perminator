<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-09-22
 * 시간: 오후 2:05
 */

namespace classes\database\query\mapper\node\attribute;


use classes\database\query\mapper\DynamicQueryContext;
use classes\database\query\mapper\exception\DynamicQueryBuilderParseException;

/**
 * Class QueryNodeTestConditional
 * @package classes\database\query\mapper\node\attribute
 */
class QueryNodeAttributeTestConditional extends AbstractQueryNodeAttribute {
	/** @var string 내용 */
	private $text;


	function __construct($text) {
		$endChar = substr(trim($text), -1);
		if($endChar != ';') $text .= ';';
		if(substr_count($text, ';') != 1) throw new DynamicQueryBuilderParseException('if node parse error');

		$this->text = $text;
	}

	/**
	 * @param DynamicQueryContext $context
	 * @return mixed
	 */
	function test(DynamicQueryContext $context) {
		$text = 'return '.$this->text;
		$text = str_replace('#', '$context->', $text);

		$result = $this->evalExecute($context, $text);

		return $result;
	}

	protected function getAttributeName() { return 'test'; }
}