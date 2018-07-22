<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 14. 9. 15
 * 시간: 오전 11:29
 */
namespace classes\database\query\mapper\behavior;

use classes\database\IDatabaseStatement;
use classes\database\query\mapper\DynamicQueryMapperParameterBinder;
use classes\database\query\mapper\DynamicQueryMapperParameterBinderBuilder;
use classes\database\query\mapper\exception\DynamicQueryExecutionException;
use classes\database\statement\MysqliPrepareStmt;
use classes\database\statement\MysqlPrepareStmt;
use classes\database\statement\PDOPrepareStatement;
use classes\lang\ArrayUtil;
use classes\util\DevUtil;

/**
 * Class DynamicQueryMysqlBehavior
 *
 * @package classes\database\query\mapper\behavior
 */
class DynamicQueryMysqlBehavior implements IBehavior {
	/** 디버그 비활성화 */
	const DEBUG_FALSE = 0;
	/** 간단한 디버그 */
	const DEBUG_SIMPLE = 1;
	/** 전달 받은 메시지 까지 */
	const DEBUG_FULL = 2;

	/** @var object|resource DB 연결 */
	private $connection;

	/** @var DynamicQueryMapperParameterBinder */
	private $binder;
	/** @var string[]|object[] 전달 받은 변수 */
	private $variables;
	/** @var int 영향 받은 행수 */
	private $affectedRows;
	/** @var int 삽입 ID */
	private $insertId;

	/** @var int result type IDatabaseStatement::RESULT_OBJECT, IDatabaseStatement::RESULT_ARRAY */
	private $resultType;
	/** @var string */
	private $returnClassName;
	/** @var \Closure[] */
	private $iteratorMap;
	/** @var int */
	private $debugFlag = self::DEBUG_FALSE;

	/**
	 * @param object|resource $connection      database connection
	 * @param string          $returnClassName ['\stdClass'] return type name
	 * @param int             $resultType      [IDatabaseStatement::RESULT_OBJECT] result type IDatabaseStatement::RESULT_OBJECT, IDatabaseStatement::RESULT_ARRAY
	 * @param array           $iteratorMap     [null] result resolver map
	 */
	public function __construct($connection, $returnClassName = '\stdClass', $resultType = IDatabaseStatement::RESULT_OBJECT, array $iteratorMap = null) {
		$this->connection = $connection;

		$this->resultType      = $resultType;
		$this->returnClassName = $returnClassName;
		$this->iteratorMap     = $iteratorMap;
	}

	/** @return int 삽입 ID */
	public function getInsertId() { return $this->insertId; }

	/** @return int 영향 받은 행수 */
	public function getAffectedRows() { return $this->affectedRows; }

	/**
	 * @param string $queryId       xml in query id
	 * @param array  $variables     [null] execute arguments
	 * @param string $mapperContent file name or xml content
	 * @param int    $type          [DynamicQueryMysqlBehavior::TYPE_FILE] DynamicQueryMysqlBehavior::TYPE_FILE, DynamicQueryMysqlBehavior::TYPE_STRING
	 *
	 * @throws DynamicQueryExecutionException
	 * @return array
	 */
	public function execFunction($queryId, array $variables = null, $mapperContent, $type = self::TYPE_FILE) {
		try {
			$this->binder    = DynamicQueryMapperParameterBinderBuilder::buildFunction($queryId, $variables, $mapperContent, $type);
			$this->variables = $this->binder->getVariables();
		} catch (\Exception $ex) {
			throw new DynamicQueryExecutionException($ex->getFile() . ':' . $ex->getLine() . "\n" . $ex->getMessage(), $ex->getCode(), $ex);
		}

		try {
			$stmt = $this->createPrepareStmt();
			$this->callBindParam($stmt);
			if ($this->debugFlag >= self::DEBUG_SIMPLE && $stmt instanceof MysqlPrepareStmt) {
				$msg = '--------------' . date('Y-m-d H:i:s') . "--------------------\n";
				if ($this->debugFlag >= self::DEBUG_FULL) $msg .= var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true) . "\n";
				$msg .= $stmt->getQuery() . "\n";
				$msg .= "-----------------------------------------------------\n";
				DevUtil::logSimpled(date('Ymd') . '-MysqlPrepareStmtDebug.log', $msg, true);
			}

			return $stmt->executeAndFetch($this->returnClassName, $this->resultType, $this->iteratorMap);
		} catch (\Exception $ex) {
			$msg = __CLASS__ . '::' . __METHOD__
				. ' error query id: '
				. $this->binder->getNamespace()
				. '.'
				. $queryId
				. " ({$ex->getMessage()})";

			if ($this->debugFlag >= self::DEBUG_FULL) {
				throw new DynamicQueryExecutionException($msg, 0, $ex, [
					'variables' => $variables
				]);
			} else {
				throw new DynamicQueryExecutionException($msg, 0, $ex);
			}

		}
	}

	/**
	 * @param string $queryId       xml in query id
	 * @param array  $variables     [null] execute arguments
	 * @param string $mapperContent file name or xml content
	 * @param int    $type          [DynamicQueryMysqlBehavior::TYPE_FILE] DynamicQueryMysqlBehavior::TYPE_FILE, DynamicQueryMysqlBehavior::TYPE_STRING
	 *
	 * @throws DynamicQueryExecutionException
	 */
	public function execProcedure($queryId, array $variables = null, $mapperContent, $type = self::TYPE_FILE) {
		try {
			$this->binder    = DynamicQueryMapperParameterBinderBuilder::buildProcedure($queryId, $variables, $mapperContent, $type);
			$this->variables = $this->binder->getVariables();
		} catch (\Exception $ex) {
			throw new DynamicQueryExecutionException($ex->getFile() . ':' . $ex->getLine() . "\n" . $ex->getMessage(), $ex->getCode(), $ex);
		}

		try {
			$stmt = $this->createPrepareStmt();
			$this->callBindParam($stmt);
			if ($this->debugFlag >= self::DEBUG_FALSE && $stmt instanceof MysqlPrepareStmt) {
				load_lib('func/dev');
				$msg = '--------------' . date('Y-m-d H:i:s') . "--------------------\n";
				if ($this->debugFlag >= self::DEBUG_FULL) $msg .= var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true) . "\n";
				$msg .= $stmt->getQuery() . "\n";
				$msg .= "-----------------------------------------------------\n";
				DevUtil::logSimpled(date('Ymd') . '-MysqlPrepareStmtDebug.log', $msg, true);
			}
			$stmt->execute();

			$this->affectedRows = $stmt->getAffectedRows();
			$this->insertId     = $stmt->getInsertId();
			$stmt->close();
		} catch (\Exception $ex) {
			$msg = __CLASS__ . '::' . __METHOD__
				. " error({$ex->getMessage()}) query id: "
				. $this->binder->getNamespace()
				. '.'
				. $queryId;


			if ($this->debugFlag >= self::DEBUG_FULL) {
				throw new DynamicQueryExecutionException($msg, 0, $ex, [
					'variables' => $variables
				]);
			} else {
				throw new DynamicQueryExecutionException($msg, 0, $ex);
			}
		}
	}

	/** @return IDatabaseStatement */
	private function createPrepareStmt() {
		$query = $this->createPrepareStmtQuery();

		if (is_resource($this->connection)) {
			return MysqlPrepareStmt::prepare($this->connection, $query);
		} elseif ($this->connection instanceof \mysqli) {
			return MysqliPrepareStmt::prepare($this->connection, $query);
		} elseif ($this->connection instanceof \pdo) {
			return PDOPrepareStatement::prepare($this->connection, $query);
		}

		throw new \InvalidArgumentException("not supported connection type");
	}

	/** @return string */
	private function createPrepareStmtQuery() {
		$params = $this->binder->getParams();
		$query  = $this->binder->getQuery();

		foreach ($params as $name => $type) {
			$query = str_replace('#{' . $name . '}', '?', $query);
		}

		foreach ($this->binder->getReplaceParams() as $name) {
			if (!array_key_exists($name, $this->variables)) {
				throw new \InvalidArgumentException("not exists property: '{$name}'");
			}
			if (!is_scalar($this->variables[$name]) && $this->variables[$name] !== null) {
				$var       = $this->variables[$name];
				$appendMsg = !is_array($var) ? get_class($var) : var_export($var, true);
				throw new \InvalidArgumentException("\"{$name}\" is not scalar." . $appendMsg);
			}


			$variableStr = $this->createVariableString($name);
			$query       = str_replace('${' . $name . '}', $variableStr, $query);
		}

		return $query;
	}

	/** @return array */
	private function createVars() {
		$vars = array();

		if (count($this->binder->getParamOrders()) != 0 && $this->variables == null) {
			throw new \InvalidArgumentException('parameters null');
		}


		foreach ($this->binder->getParamOrders() as $param) {
			$selectors = $this->paramToSelector($param);
			// 1차원 array 지정하는거 넣어줄것

			if (!array_element_key_exists($this->variables, $selectors))
				throw new \InvalidArgumentException("not exists param: '{$param}'");


			$selectedElement = ArrayUtil::select($this->variables, $selectors);
			if ((!is_scalar($selectedElement) && !is_array($selectedElement)) && $selectedElement !== null) {
				$var       = $selectedElement;
				$appendMsg = !is_array($var) ? get_class($var) : var_export($var, true);
				throw new \InvalidArgumentException("\"{$param}\" is not scalar." . $appendMsg);
			}

			$vars[] = $selectedElement;
		}

		return $vars;
	}

	private function paramToSelector($param) {
		$firstSelector = preg_replace('/\[[\'"]?([a-zA-Z0-9_]*)[\'"]\]/', "", $param);
		preg_match_all('/\[[\'"]?([a-zA-Z0-9_]*)[\'"]\]/', $param, $subSelector);
		if(count($subSelector) > 1) {
			array_unshift($subSelector[1], $firstSelector);
			return $subSelector[1];
		} else {
			return [$firstSelector];
		}
	}

	/**
	 * 전달 받은 값을 바인드
	 *
	 * @param IDatabaseStatement $stmt
	 */
	private function callBindParam(IDatabaseStatement $stmt) {
		if (count($this->binder->getParamTypeOrders()) == 0 || $this->variables == null) return;

		$types = implode('', $this->binder->getParamTypeOrders());

		$args = $this->createVars();
		array_unshift($args, $types);

		try {
			call_user_func_array(array($stmt, "bindParam"), $args);
		} catch (\Exception $ex) {
			if ($stmt instanceof MysqlPrepareStmt) {
				$msg = $stmt->getQuery();
			} else {
				$msg = $stmt->getError();
			}

			throw new DynamicQueryExecutionException($msg, 0, $ex);
		}
	}

	/**
	 * @param int $debugFlag DynamicQueryMysqlBehavior::DEBUG_FALSE, DynamicQueryMysqlBehavior::DEBUG_SIMPLE, DynamicQueryMysqlBehavior::DEBUG_FULL
	 */
	public function setDebug($debugFlag) { $this->debugFlag = $debugFlag; }

	/**
	 * @param $name
	 *
	 * @return object|string
	 */
	private function createVariableString($name) {
		if ($this->variables[$name] === null) {
			$variableStr = 'null';

			return $variableStr;
		} elseif ($this->variables[$name] === true) {
			$variableStr = 'true';

			return $variableStr;
		} elseif ($this->variables[$name] === false) {
			$variableStr = 'false';

			return $variableStr;
		} else {
			$variableStr = $this->variables[$name];

			return $variableStr;
		}
	}
}