<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-11
 * Time: 오전 3:10
 */

namespace classes\database\query\mapper\node;


use classes\database\query\mapper\DynamicQueryContext;

/**
 * @FIXME
 * Class QueryNodeBind
 * @package classes\database\query\mapper\node
 */
class QueryNodeBind implements IQueryNode {


	/**
	 * QueryNodeBind constructor.
	 */
	public function __construct() {
		throw new \UnsupportedOperationException();
	}

	function setAttributes(array $attributes) {
		// TODO: Implement setAttributes() method.
	}

	function addNode(IQueryNode $node) { throw new \InvalidArgumentException("QueryNodeBind is not have child node"); }

	function setText($text) { throw new \InvalidArgumentException("QueryNodeBind is not have child node"); }

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