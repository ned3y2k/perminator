<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-11
 * Time: 오전 3:05
 */

namespace classes\database\query\mapper\node;


use classes\database\query\mapper\DynamicQueryContext;
use classes\database\query\mapper\exception\DynamicQueryBuilderException;
use classes\database\query\mapper\exception\DynamicQueryExecuteInvalidArgumentException;
use classes\database\query\mapper\exception\node\QueryNodeAttributeStatementException;
use classes\database\query\mapper\exception\node\QueryNodeChildRuleException;

/**
 * http://www.phpliveregex.com/
 * Class QueryNodeForeach
 * @package classes\database\query\mapper\node
 */
class QueryNodeForeach implements IQueryNode {
	/** @var IQueryNode[] */
	private $childNodes = array();
	/** @var DynamicQueryContext */
	private $context;
	/** @var array */
	private $attributes;

	function setAttributes(array $attributes) {
		$this->attributes = array_change_key_case($attributes, CASE_LOWER);
	}

	function addNode(IQueryNode $node) {
		if (!$this->isAllowChild($node)) {
			throw new QueryNodeChildRuleException($node->nodeName() . " node can not be contained foreach node");
		}

		$node->setContext($this->context);
		$this->childNodes[] = $node;
	}

	function setText($text) { throw new DynamicQueryBuilderException(get_called_class() . ' not implemented setText'); }

	function getChildNodes() { return $this->childNodes; }

	function setContext(DynamicQueryContext $context) {
		$this->context = $context;
		foreach ($this->childNodes as $childNode) {
			$childNode->setContext($context);
		}
	}

	public function __toString() {
		if (!array_key_exists('collection', $this->attributes)) {
			throw new QueryNodeAttributeStatementException("not found collection attribute");
		}
		$collectionName = $this->attributes['collection'];
		$separator      = array_key_exists('separator', $this->attributes) ? $this->attributes['separator'] : '';
		$open           = array_key_exists('open', $this->attributes) ? $this->attributes['open'] : '';
		$close          = array_key_exists('close', $this->attributes) ? $this->attributes['close'] : '';

		$childNodeToStringRetriever = function () {
			$buff = "";
			foreach ($this->childNodes as $childNode) {
				$tmp = $childNode->__toString();

				if (strlen($tmp) > 0)
					$buff .= $tmp;
			}

			return $buff;
		};

		if (array_key_exists('item', $this->attributes)) {
			$context = $this->context;
			$this->validationAttibute($collectionName);


//			$eval       = 'return ' . str_replace('#', '$context->', $collectionName) . ';';
//			$collection = eval($eval);

			if(strpos($collectionName, '#') === false) {
				throw new \InvalidArgumentException('Collection did not start with #');
			} elseif(!($context instanceof DynamicQueryContext)) {
				throw new \InvalidArgumentException('context is not \classes\database\query\mapper\DynamicQueryContext');
			} elseif(isset($context->$collectionName)) {
				throw new \InvalidArgumentException("context has not {$collectionName}");
			}

			$collectionName = substr($collectionName, 1);
			$collection = $context->$collectionName;


			$looper = new QueryNodeForeachItemLooper($childNodeToStringRetriever, str_replace('#', '', $this->attributes['item']), $separator, $this->context, $collection);
		} else {
			throw new QueryNodeAttributeStatementException("Only item is available");
		}

		$looperResult = $looper->__toString();

		return strlen(strlen($looperResult) > 0) ? $open . $looperResult . $close : '';
	}

	/**
	 * @param IQueryNode $node
	 *
	 * @return bool
	 */
	private function isAllowChild(IQueryNode $node): bool {
		return
			$node instanceof QueryNodeForeach
			|| $node instanceof QueryNodeBind
			|| $node instanceof QueryNodeIf
			|| $node instanceof QueryNodeSql
			|| $node instanceof QueryNodeInclude
			|| $node instanceof QueryNodeSet
			|| $node instanceof QueryNodeTrim
			|| $node instanceof QueryNodeWhere
			|| $node instanceof QueryTextNode;
	}

	public function nodeName() { return strtolower(str_replace(__NAMESPACE__ . "\\QueryNode", "", get_class($this))); }

	/**
	 * @param $collectionName
	 */
	private function validationAttibute($collectionName) {
		if (strpos($collectionName, '#') === false) {
			throw new DynamicQueryExecuteInvalidArgumentException("foreach node exception. invalid collection name: {$collectionName}");
		} else if (strpos($this->attributes['item'], '#') === false) {
			throw new DynamicQueryExecuteInvalidArgumentException("foreach node exception. invalid item name: {$this->attributes['item']}");
		}
	}
}

class QueryNodeForeachItemLooper {
	/** @var \Closure */
	private $childNodeToStringRetriever;
	/** @var array */
	private $collection;
	/** @var DynamicQueryContext */
	private $context;
	private $itemKey;
	private $separator;

	public function __construct(\Closure $childNodeToStringRetriever, $itemKey, $separator, DynamicQueryContext $context, array $collection = null) {
		$this->childNodeToStringRetriever = $childNodeToStringRetriever;
		$this->collection                 = $collection;
		$this->context                    = $context;
		$this->itemKey                    = $itemKey;
		$this->separator                  = $separator;
	}

	function __toString() {
		$childNodeToStringRetriever = $this->childNodeToStringRetriever;

		$buff = '';

		foreach ($this->collection as $item) {
			$localItemKey = uniqid($this->itemKey);
			$old = $childNodeToStringRetriever();
			$nodeBody     = str_replace($this->createItemParamName(), '#{' . $localItemKey . '}', $old, $cnt) . $this->separator;
			if (!$cnt) {
				$nodeBody = preg_replace("/(#\\{)([a-z][:]!?|)([A-z0-9_]+)(\\[.*\\]?\\})/", "$1$2{$localItemKey}$4$5", $old) . $this->separator;
			}
			$buff .= $nodeBody;
			$this->context->{$localItemKey} = $item;
		}

		return trim($buff, $this->separator);
	}

	private function createItemParamName() {
		return "#{" . $this->itemKey . "}";
	}
}