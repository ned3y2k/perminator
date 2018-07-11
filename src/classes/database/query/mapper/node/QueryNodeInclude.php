<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-11
 * Time: 오전 3:16
 */

namespace classes\database\query\mapper\node;


use classes\database\query\mapper\DynamicQueryContext;

class QueryNodeInclude implements IQueryNode {

	public function __construct() {
		throw new \UnsupportedOperationException();
	}

	function setAttributes(array $attributes) {
		// TODO: Implement setAttributes() method.
	}

	function addNode(IQueryNode $node) { throw new \InvalidArgumentException("QueryNodeInclude not have child node"); }

	function setText($text) { throw new \InvalidArgumentException("QueryNodeInclude not have child node"); }

	function getChildNodes() {
		// TODO: Implement getChildNodes() method.
	}

	function setContext(DynamicQueryContext $context) {
		// TODO: Implement setContext() method.
	}

	public function __toString() {
		// TODO: Implement __toString() method.
	}

	public function nodeName() { return strtolower(str_replace(__NAMESPACE__."\\QueryNode", "", get_class($this))); }
}