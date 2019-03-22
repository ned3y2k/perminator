<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 13
 * 시간: 오후 7:28
 */
namespace classes\database\query\mapper\node;


use classes\database\query\mapper\DynamicQueryContext;

interface IQueryNode {
	function setAttributes(array $attributes);
	function addNode(IQueryNode $node);
	/**
	 * CDATA 를 넣을때 사용
	 * @param $text
	 */
	function setText($text);
	function getChildNodes();
	function setContext(DynamicQueryContext $context);
	function nodeName();

	public function __toString();
} 