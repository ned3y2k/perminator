<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-11-25
 * 시간: 오전 10:43
 */

namespace classes\database\query\mapper\parser;

use classes\database\conf\mapper\DynamicQueryConst;
use classes\database\query\mapper\exception\DynamicQueryBuilderException;
use classes\database\query\mapper\exception\DynamicQueryBuilderParseException;
use classes\database\query\mapper\node\IQueryNode;
use classes\database\query\mapper\node\QueryNodeFunction;
use classes\database\query\mapper\node\QueryNodeMapper;
use classes\database\query\mapper\node\QueryNodeProcedure;
use classes\database\query\mapper\node\QueryTextNode;
use classes\database\query\mapper\QueryNodeFactory;
use classes\lang\ArrayUtil;

class XmlParser implements IXmlParser {
	private $parser;

	/** @var IQueryNode[] */
	private $nodes;
	/** @var IQueryNode */
	private $currentNode;

	/** @var QueryNodeMapper */
	private $mapper;

	function __construct($xml) {
		$this->parser = xml_parser_create('utf-8');
		$xml = trim(substr($xml, strpos($xml, '<mapper')));
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, "tagOpen", "tagClose");
		xml_set_character_data_handler($this->parser, "cdata");
		xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, false);
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		xml_parse($this->parser, $xml);
	}

	/** @return QueryNodeMapper */
	public function getMapper() {
		return $this->mapper;
	}

	private function tagOpen($parser, $tag, $attributes) {
		if($tag == DynamicQueryConst::TAG_MAPPER) {
			if($this->mapper == null) {
				$this->mapper = new QueryNodeMapper();
				$this->mapper->setAttributes($attributes);
			} else
				throw new DynamicQueryBuilderException("invalid xml file structure. only one can have mapper node per file");
		} elseif($tag == DynamicQueryConst::TAG_FUNCTION) {
			$node = new QueryNodeFunction();
			$node->setAttributes($attributes);
			$this->mapper->putNode($node->id, $node);

			$this->currentNode = $node;
			$this->nodes[] = $node;
		} elseif($tag == DynamicQueryConst::TAG_PROCEDURE) {
			$node = new QueryNodeProcedure();
			$node->setAttributes($attributes);
			$this->mapper->putNode($node->id, $node);

			$this->currentNode = $node;
			$this->nodes[] = $node;
		} else {
			$node = QueryNodeFactory::create($tag, $attributes);

			if($this->currentNode == null) {
				throw new DynamicQueryBuilderParseException("<{$tag} id=\"{$this->mapper->getNamespace()}.{$attributes['id']}\">, during parsing problems");
			}
			$this->currentNode->addNode($node);

			$this->currentNode = $node;
			$this->nodes[] = $node;
		}
	}

	/**
	 * @param $parser
	 * @param $tag
	 * @throws \Exception
	 */
	private function tagClose($parser, $tag) {
		try {
			if($tag != DynamicQueryConst::TAG_MAPPER)
				array_pop($this->nodes);
		} catch (\Exception $ex) {
			throw new \Exception(strtolower($tag)." close exception ");
		}

		$this->currentNode = ArrayUtil::getValue($this->nodes, count($this->nodes) - 1); // notice 나면if count != 0 추가하기
	}

	private function cdata($parser, $cdata) {
		// echo str_repeat("  ", count($this->nodes)).get_class($this->currentNode).":".$cdata."\n";
		if(strlen($cdata) <= 0) return;

		$node = new QueryTextNode();
		$node->setText($cdata);


		if($this->currentNode == null) return;
		$this->currentNode->addNode($node);
	}
} 