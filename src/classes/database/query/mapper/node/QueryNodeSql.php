<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-11
 * Time: 오전 3:15
 */

namespace classes\database\query\mapper\node;


use classes\database\query\mapper\DynamicQueryContext;

/**
 * @FIXME
 * Class QueryNodeSql
 * @package classes\database\query\mapper\node
 */
class QueryNodeSql implements IQueryNode {
	/**
	 * QueryNodeSql constructor.
	 */
	public function __construct() {
		throw new \UnsupportedOperationException();
	}

	function setAttributes(array $attributes) {
		// TODO: Implement setAttributes() method.
	}

	function addNode(IQueryNode $node) {
		// TODO: Implement addNode() method.
	}

	function setText($text) {
		// TODO: Implement setText() method.
	}

	function getChildNodes() {
		// TODO: Implement getChildNodes() method.
	}

	function setContext(DynamicQueryContext $context) {
		// TODO: Implement setContext() method.
	}

	function nodeName() {
		// TODO: Implement nodeName() method.
	}

	public function __toString() {
		// TODO: Implement __toString() method.
	}
}