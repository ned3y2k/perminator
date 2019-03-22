<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 13
 * 시간: 오후 7:33
 */
namespace classes\database\query\mapper\node;

use classes\database\conf\mapper\DynamicQueryConst;
use classes\database\query\mapper\DynamicQueryContext;
use classes\database\query\mapper\exception\DynamicQueryBuilderException;
use classes\database\query\mapper\exception\DynamicQueryExecuteInvalidArgumentException;
use classes\database\query\mapper\exception\node\QueryNodeDuplicatedIdException;
use classes\database\query\mapper\exception\node\QueryNodeMapperException;

/**
 * Class QueryNodeMapper
 *
 * @package classes\database\query\mapper\node
 */
class QueryNodeMapper {
	/** @var IQueryNode[] */
	private $queryNodes = array();
	/** @var string */
	private $namespace;
    /** @var QueryNodeFunction[] */
    private $functions = array();
	/** @var QueryNodeProcedure[] */
	private $procedures = array();

	/**
	 * @param array $attributes
	 */
	public function setAttributes(array $attributes) {
		if(!array_key_exists(DynamicQueryConst::ATTR_NAMESPACE, $attributes)) throw new QueryNodeMapperException('namespace was empty');
		$this->namespace = $attributes[DynamicQueryConst::ATTR_NAMESPACE];
	}

	/**
	 * @param string     $id
	 * @param IQueryNode $queryNode
	 */
	public function putNode($id, IQueryNode $queryNode) {
		if(array_key_exists($id, $this->queryNodes)) throw new QueryNodeDuplicatedIdException("duplicated queryId");

        if($queryNode instanceof QueryNodeFunction) {
            $this->functions[$id] = $queryNode;
        } elseif($queryNode instanceof QueryNodeProcedure) {
	        $this->procedures[$id] = $queryNode;
		} else {
            throw new \UnsupportedOperationException('not yet implemented');
        }

		$this->queryNodes[$id] = $queryNode;
	}

	/**
	 * @param string $id
	 * @param array $variables
	 * @return string
	 */
	public function getFunction($id, array &$variables = null) {
	    if($this->functions == null) throw new DynamicQueryExecuteInvalidArgumentException("{$id} function is not in {$this->namespace}");
	    elseif(!is_string($id)) throw new DynamicQueryExecuteInvalidArgumentException("invalid query invoke id");
        elseif(!array_key_exists($id, $this->functions)) throw new DynamicQueryBuilderException("'{$this->namespace}' in not found function query function. '{$id}'");

        $func = $this->functions[$id];
        $context = new DynamicQueryContext($variables);
        $func->setContext($context);
		$query = $func->__toString();
		$variables = $context->getVariables();

        return $query;
    }

	/**
	 * @param string $id
	 * @param array $variables
	 * @return string
	 */
	public function getProcedure($id, array &$variables = null) {
		if($this->procedures == null) throw new DynamicQueryExecuteInvalidArgumentException("\"{$id} procedure is not in {$this->namespace}\"");
		elseif(!is_string($id)) throw new DynamicQueryExecuteInvalidArgumentException("invalid query invoke id");
		elseif(!array_key_exists($id, $this->procedures)) throw new DynamicQueryBuilderException("'{$this->namespace}' in not found procedure query status. '{$id}'");

		$func = $this->procedures[$id];
		$context = new DynamicQueryContext($variables);
		$func->setContext($context);
		$query = $func->__toString();
		$variables = $context->getVariables();

		return $query;
	}

	/** @return string */
	public final function getNamespace() { return $this->namespace; }
}