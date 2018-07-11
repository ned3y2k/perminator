<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-09-22
 * 시간: 오후 1:19
 */

namespace classes\database\query\mapper\node;

use classes\database\query\mapper\DynamicQueryContext;

/**
 * Class QueryNodeChoose
 * @package classes\database\query\mapper\node
 */
class QueryNodeChoose implements IQueryNode {
	/** @var QueryNodeWhen[] */
	private $whenNodes = array();
	/** @var QueryNodeOtherwise */
	private $otherwiseNode;
	/** @var DynamicQueryContext */
	private $context;

	/** @param array $attributes */
	public function setAttributes(array $attributes) { }

	/** @param IQueryNode $node */
	public function addNode(IQueryNode $node)
	{
		if($node instanceof QueryTextNode) {
			return ;
		}  elseif($node instanceof QueryNodeWhen) {
			$this->whenNodes[] = $node;
		} elseif($node instanceof QueryNodeOtherwise && $this->otherwiseNode == null) {
			$this->otherwiseNode = $node;
		} else {
			throw new \InvalidArgumentException();
		}
	}

	/** @return string */
	public function __toString() {
		$requireOtherwise = true;

		$buff = '';
		foreach($this->whenNodes as $whenNode) {
			$tmp = $whenNode->__toString();

			if(strlen($tmp) > 0) {
				$buff .= DynamicQueryContext::sep.$tmp;
				$requireOtherwise = false;
			}
		}

		if($requireOtherwise && $this->otherwiseNode != null) $buff = $this->otherwiseNode->__toString();

		return $buff;
	}

	/** @param string $text */
	function setText($text) { throw new \UnsupportedOperationException(__CLASS__.__METHOD__.':'.__LINE__); }

	/** @return IQueryNode[] */
	function getChildNodes() {
		if($this->otherwiseNode != null)
			return array_merge($this->whenNodes, array($this->otherwiseNode));
		else
			return $this->whenNodes;
	}

	/** @param DynamicQueryContext $context */
	function setContext(DynamicQueryContext $context)
	{
		$this->context = $context;

		if($this->otherwiseNode != null)
			$this->otherwiseNode->setContext($context);

		foreach($this->whenNodes as $whenNode) {
			$whenNode->setContext($context);
		}
	}

	public function nodeName() { return strtolower(str_replace(__NAMESPACE__."\\QueryNode", "", get_class($this))); }
}