<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 14. 9. 15
 * 시간: 오전 9:32
 */
namespace classes\database\query\mapper;

use classes\database\query\mapper\exception\DynamicQueryBuilderException;
use classes\database\query\mapper\node\QueryNodeMapper;

/**
 * Class DynamicQueryMapperParameterBinder
 *
 * @package classes\database\query\mapper
 */
class DynamicQueryMapperParameterBinder {
	/** 함수 select (리턴값 있음) */
	const TYPE_FUNCTION = 0;
	/** 프로시져 insert, delete, update (리턴값 없음) */
	const TYPE_PROCEDURE = 1;

	/** @var DynamicQueryMapperParameter[] */
	private $params = array();
	/** @var string 쿼리 */
	private $query;
	/** @var array */
	private $paramOrders = array();
	/** @var string[] */
	private $paramTypeOrders = array();
	/** @var string */
	private $namespace;
	/** @var array */
	private $replaceParams = array();
	/** @var string */
	private $queryId;
	/**
	 * @var array
	 */
	private $variables;

	/**
	 * @param QueryNodeMapper $mapper
	 * @param string          $queryId
	 * @param array           $variables
	 * @param int             $type
	 */
	public function __construct(QueryNodeMapper $mapper, $queryId, array $variables = null, $type = self::TYPE_FUNCTION) {
		$this->namespace = $mapper->getNamespace();
		$this->queryId   = $queryId;

		if ($type == self::TYPE_FUNCTION)
			$this->query = $mapper->getFunction($queryId, $variables);
		elseif ($type == self::TYPE_PROCEDURE)
			$this->query = $mapper->getProcedure($queryId, $variables);
		else
			throw new \InvalidArgumentException($type);

		$this->prepareParams($variables);
		$this->prepareReplaceParam($variables);
		$this->variables = $variables;
	}

	public function getVariables() { return $this->variables; }

	/** @return string */
	public function getQuery() { return $this->query; }

	/** @return DynamicQueryMapperParameter[] */
	public function getParams() { return $this->params; }

	/** @return array */
	public function getParamOrders() { return $this->paramOrders; }

	/** @return string[] */
	public function getParamTypeOrders() { return $this->paramTypeOrders; }

	/** @return string */
	public final function getNamespace() { return $this->namespace; }

	/** @return array */
	public function getReplaceParams() { return $this->replaceParams; }

	/** @param array $variables */
	private function prepareParams(array $variables = null) {
//		$pattern = '/#\{(?:[a-z]\:?)[a-zA-Z0-9_]+\}/';
		$pattern = '/#\{([a-z]:|)?[A-z0-9_]+[\\\'"A-z0-9_]*\}/';
		preg_match_all($pattern, $this->query, $matches, PREG_OFFSET_CAPTURE);

//		$paramPattern = '/([a-z]\:)?([a-zA-Z0-9_]+)/';
		$paramPattern = '/([a-z]\:|)?([a-zA-Z0-9_]+)([\\\'"A-z0-9_]*)/';
		$indexPattern = '/\[\'([A-z0-9]*)\'\]/';
//		$paramPattern = '/([a-z]\:|)?([a-zA-Z0-9_]+)([\\\'"A-z0-9_]*)/';

		if (count($matches[0]) && !$variables)
			throw new DynamicQueryBuilderException('Query parameter declared but no execution arguments');

		foreach ($matches[0] as $param) {
			preg_match($paramPattern, $param[0], $paramDetail);
			preg_match($indexPattern, $param[0], $index);

			$name = $paramDetail[2];
			if (!$index && !array_key_exists($name, $variables))
				throw new DynamicQueryBuilderException($this->namespace . '.' . $this->queryId . '.#{' . $name . '} value not found');
			elseif ($index && (!array_key_exists($name, $variables) || !array_key_exists($index[1], $variables[$name]))) {
				throw new DynamicQueryBuilderException($this->namespace . '.' . $this->queryId . '.#{' . $name . '[' . $index[1] . ']} value not found');
			}

			$position = $param[1];
			$type     = strlen($paramDetail[1]) > 0 ? substr($paramDetail[1], 0, 1) : 's';


			if ($index) {
				$name        = "{$name}['{$index[1]}']";
				$this->query = str_replace($paramDetail[0], $name, $this->query);
			} else {
				$this->query = str_replace($paramDetail[0], $name, $this->query);
			}

			if (!array_key_exists($name, $this->params)) {
				$this->params[$name]     = new DynamicQueryMapperParameter($name, $type, $position);
				$this->paramOrders[]     = $name;
				$this->paramTypeOrders[] = $type;
			} else {
				$this->params[$name]->addPosition($position);
				$this->paramOrders[]     = $name;
				$this->paramTypeOrders[] = $type;
			}
		}
	}

	/** @param array $variables */
	private function prepareReplaceParam(array $variables = null) {
		$pattern = '/\$\{([a-z]:|)?[A-z0-9_]+[\\\'"A-z0-9_]*\}/';
		preg_match_all($pattern, $this->query, $matches, PREG_OFFSET_CAPTURE);

//		$paramPattern = '/([a-zA-Z0-9_]+)/';
		$paramPattern = '/([a-z]\:|)?([a-zA-Z0-9_]+[\\\'"A-z0-9_]*)/';

		foreach ($matches[0] as $param) {
			preg_match($paramPattern, $param[0], $paramDetail);

			$name = $paramDetail[1];
			if ($variables == null || !array_key_exists($name, $variables))
				throw new DynamicQueryBuilderException($this->namespace . '.' . $this->queryId . '.${' . $name . '} value not found');

			if (!in_array($name, $this->replaceParams)) {
				$this->replaceParams [] = $name;
			}
		}
	}
}



