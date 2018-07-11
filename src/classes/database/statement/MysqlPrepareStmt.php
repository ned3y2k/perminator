<?php
namespace classes\database\statement;

use classes\database\IDatabaseStatement;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\util\object\ObjectBuilderUtil;


class MysqlPrepareStmt implements IDatabaseStatement {
	private $conn;
	private $query;
	private $orgQuery;
	private $reusable;

	private $result;

	private $types      = array();
	private $typesCount = 0;

	private $bindParams;

	/**
	 * @param resource $connection
	 * @param string   $query
	 * @param bool     $reusable
	 *
	 * @return \classes\database\statement\MysqlPrepareStmt
	 */
	public static function prepare($connection, $query, $reusable = false) {
		return new self($connection, $query, $reusable);
	}

	public function storeResult() { }

	public function close() { }

	/**
	 * @param resource $connection
	 * @param string   $query
	 * @param bool     $reusable
	 *
	 * @throws MysqlPrepareStmtException
	 */
	public function __construct($connection, $query, $reusable = false) {
		if (!is_resource($connection)) throw new MysqlPrepareStmtException();

		$this->conn = $connection;
		$this->query = $query;
		$this->orgQuery = $query;
		$this->reusable = $reusable;
	}

	public function getAffectedRows() {
		return mysql_affected_rows($this->conn);
	}

	public function getInsertId() {
		$result = mysql_query("SELECT LAST_INSERT_ID() AS `id`", $this->conn);
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		mysql_free_result($result);

		return $row['id'];
	}

	public function getNumRows() {
		return mysql_num_rows($this->result);
	}

	public function getErrno() {
		return mysql_errno($this->conn);
	}

	public function getError() {
		return mysql_error($this->conn);
	}

	public function getSqlstate() {
		return $this->getErrno();
	}

	public function dataSeek($offset) {
		return mysql_data_seek($this->result, $offset);
	}

	public function freeResult() {
		if (isset($this->result)) {
			if (is_resource($this->result)) mysql_free_result($this->result);
			unset($this->result);
		}
	}

	public function reset() {
		if (isset($this->result)) $this->freeResult();
	}

	/**
	 * Binds variables to a prepared statement as parameters
	 * @link http://www.php.net/manual/en/mysqli-stmt.bind-param.php
	 *
	 * @return $this
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function bindParam() {
		$args = func_get_args();
		$this->typesCount = strlen($args[0]);

		for ($i = 0; $i < $this->typesCount; $i++) {
			$type = substr($args[0], $i, 1);
			$this->performCheckArgumentType($type, $i);

			$this->types[] = $type;
		}
		$this->bindParams = array_slice($args, 1);


		if (!$this->parametersCountCheck()) {
			static $msgf = "not paired number parameter. typeCount: %s, bindParamCount: %s  param count: %s";
			$msg = sprintf($msgf, count($this->types), count($this->bindParams), substr_count($this->query, '?'));
			throw new MysqlPrepareStmtException($msg);
		}

		for ($i = 0; $i < count($this->bindParams); $i++) {
			if($this->bindParams[$i] !== null && !is_scalar($this->bindParams[$i])) throw new \InvalidArgumentException('allow only scalar type - {'.var_export($this->bindParams[$i],true).'}');
			else if($this->bindParams[$i] === null) {
				$this->bindParams[$i] = 'null';
			}  elseif ($this->types[$i] == 'i') { // integer
				if (!is_numeric($this->bindParams[$i]))
					throw new MysqlPrepareStmtException("truncated integer value[{$i}] " . var_export($this->bindParams[$i], true));
				$this->bindParams[$i] = intval($this->bindParams[$i]);
			} elseif ($this->types[$i] == 'd') { // double
				if (!is_numeric($this->bindParams[$i])) throw new MysqlPrepareStmtException("truncated double value");
				$this->bindParams[$i] = doubleval($this->bindParams[$i]); // string and blog
			} else $this->bindParams[$i] = "'" . mysql_real_escape_string($this->bindParams[$i], $this->conn) . "'";
		}

		$this->performParameterMapping();

		return $this;
	}

	/**
	 * @return int
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function execute() {
		if (defined('_VERBOSE_') && _VERBOSE_ == true) {
			$msc = microtime(true);
		}

		$this->result = mysql_query($this->query, $this->conn);

		if (defined('_VERBOSE_') && _VERBOSE_ == true) {
			$msc = microtime(true) - $msc;

			load_lib('func/dev');
			$msg = 'endTime: ' . $msc;
			$msg .= "\n-----------------------------------------------------\n";
			dev_log_simpled(date('Ymd') . '.log', $msg, true);
		}
		try {
			$this->checkStmtError();
		} catch (MysqlPrepareStmtException $ex) {
			throw new MysqlPrepareStmtException($ex->getMessage(), $ex->getCode(), $this, $ex);
		}


		$this->query = $this->orgQuery;
		$this->types = array();

		return $this->getInsertId() ? $this->getInsertId() : $this->getAffectedRows();
	}


	/**
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	private function checkStmtError() {
		if ($this->getErrno()) {
			load_lib('func/dev');
			$msg = '--------------' . date('Y-m-d H:i:s') . "--------------------\n";
			$msg .= $this->query . "\n";
			$msg .= "-----------------------------------------------------\n";

			dev_log_simpled(date('Ymd') . '.log', $msg, true);
			throw new MysqlPrepareStmtException($this->getError(), $this->getErrno(), $this);
		}
	}

	public function fetchArray(array $iteratorMap = null) {
		$result = array();

		$rows = $this->resourceFetchArray();
		foreach ($rows as $row) {
			$fetchedRow = array();

			foreach ($row as $fieldName => $value) {
				if ($iteratorMap == null || !array_key_exists($fieldName, $iteratorMap)) {
					array_push($fetchedRow, $value);
					$fetchedRow [$fieldName] = $value;
				} else {
					$relativeValue = call_user_func($iteratorMap[$fieldName]->closure, $value);
					array_push($fetchedRow, $relativeValue);
					$fetchedRow [$fieldName] = $relativeValue;
				}
			}

			array_push($result, $fetchedRow);
		}


		$this->freeResult();
		if (!$this->reusable) {
			$this->close();
		} else $this->reset();

		if (count($result) == 0) return null;

		return $result;
	}

	/**
	 * @param string $className
	 * @param ObjectBuilderUtil|null $objectBuilder
	 * @param array|null $iteratorMap
	 * @return array|null
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 * @throws \ReflectionException
	 */
	public function fetchObject($className = self::DEFAULT_CLASS_NAME, ObjectBuilderUtil $objectBuilder = null, array $iteratorMap = null) {
		$objectBuilder = $className != self::DEFAULT_CLASS_NAME ? new ObjectBuilderUtil ($className) : null;

		$rows = $this->resourceFetchArray();

		$result = array();

		if ($className == self::DEFAULT_CLASS_NAME) {
			foreach ($rows as $row) {
				$fetchedRow = new $className ();

				foreach ($row as $fieldName => $value) {
					if ($iteratorMap == null || !array_key_exists($fieldName, $iteratorMap)) {
						$fetchedRow->$fieldName = $value;
					} else {
						$fetchedRow->$fieldName = call_user_func($iteratorMap[$fieldName]->closure, $value);
					}
				}

				array_push($result, $fetchedRow);
			}
		} else {
			foreach ($rows as $row) {
				$propertyList = array();

				foreach ($row as $fieldName => $value) {
					if ($iteratorMap == null || !array_key_exists($fieldName, $iteratorMap)) {
						$propertyList [$fieldName] = $value;
					} else {
						$propertyList [$iteratorMap[$fieldName]->fieldName] = call_user_func($iteratorMap[$fieldName]->closure, $value);
					}
				}

				$fetchedRow = $objectBuilder->build($propertyList);
				unset ($propertyList);
				array_push($result, $fetchedRow);
			}
		}

		$this->freeResult();
		if (!$this->reusable) {
			$this->close();
		} else $this->reset();

		if (count($result) == 0) return null;

		return $result;
	}

	/**
	 * @param string $className
	 * @param int $type
	 * @param array|null $iteratorMap
	 * @return array|null
	 * @throws \Exception
	 */
	public function executeAndFetch($className = self::DEFAULT_CLASS_NAME, $type = self::RESULT_OBJECT, array $iteratorMap = null) {
		try {
			$this->execute();
		} catch (\Exception $ex) {
			throw $ex;
		}

		switch ($type) {
			case self::RESULT_OBJECT :
				$objectBuilder = $className != self::DEFAULT_CLASS_NAME ? new ObjectBuilderUtil ($className) : null;

				return $this->fetchObject($className, $objectBuilder, $iteratorMap);
			case self::RESULT_ARRAY:
				return $this->fetchArray($iteratorMap);
			default:
				throw new MysqlPrepareStmtException('Invalid Return Type');
		}
	}

	public function getQuery() {
		return $this->query;
	}

	/**
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	private function performParameterMapping() {
		$query = $this->query;
		$args = $this->bindParams;
		$argsCount = count($args);

		$splitedQuery = explode('?', $query);
		$tmpQuery = '';

		for ($i = 0; $i < $argsCount; $i++) {
			try {
				$tmpQuery .= $splitedQuery[$i] . $args[$i];
			} catch (\NoticeException $ex) {
				throw new \mysqli_sql_exception('may not matched param count', 0, new \InvalidArgumentException());
			}
		}

		for (; $i < count($splitedQuery); $i++) {
			$tmpQuery .= $splitedQuery[$i];
		}

		if (defined('_VERBOSE_') && _VERBOSE_ == true) {
			load_lib('func/dev');
			$msg = '--------------' . date('Y-m-d H:i:s') . "--------------------\n";
			$msg .= $tmpQuery . "\n";
			dev_log_simpled(date('Ymd') . '.log', $msg, true);
		}

		$this->query = $tmpQuery;
	}

	private function resourceFetchArray() {
		$rows = array();
		$row = mysql_fetch_array($this->result);
		while ($row) {
			$this->setMemoryLimit();

			$rows[] = $row;
			$row = mysql_fetch_array($this->result);
		}

		return $rows;
	}

	/**
	 * @param string $type
	 * @param string $extra
	 *
	 * @throws MysqlPrepareStmtException
	 */
	private function performCheckArgumentType($type, $extra = '') {
		switch ($type) {
			case 'i':
				break;
			case 'd':
				break;
			case 's':
				break;
			case 'b':
				break;
			default:
				throw new MysqlPrepareStmtException('invalid type arguement ' . $extra);
		}
	}

	public function escapeString($string) { return mysql_real_escape_string($string, $this->conn); }

	/**
	 * @return bool
	 */
	protected function parametersCountCheck() {
		return count(array_unique(array(count($this->types), count($this->bindParams), count($this->types), substr_count($this->query, '?')))) == 1;
	}

	private function setMemoryLimit() {
		$memory_limit = ini_get('memory_limit');
		if (($memory_limit - memory_get_usage()) <= ($memory_limit - 1000)) {
			ini_set('memory_limit', $memory_limit + 20000);
		}
	}
}