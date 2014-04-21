<?php
namespace component\database\statement;
use component\database\IDatabaseStatement;
use classes\util\ObjectBuilderUtil;

class MysqlPrepareStmt implements IDatabaseStatement {
	private $conn;
	private $query;
	private $reusable;

	private $result;

	private $types = array();

	private $bindParams;

	/**
	 * @param resource $connection
	 * @param string $query
	 * @param string $reusable
	 * @return \classes\database\statement\MysqlPrepareStmt
	 */
	public static function prepare($connection, $query, $reusable = false) {
		return new self($connection, $query, $reusable);
	}

	public function storeResult() {}
	public function close() {}

	/**
	 * @param resource $connection
	 * @param string $query
	 * @param string $reusable
	 */
	public function __construct($connection, $query, $reusable = false) {
		if(!is_resource($connection)) throw new \InvalidArgumentException();

		$this->conn = $connection;
		$this->query = $query;
		$this->reusable = $reusable;
	}

	public function getAffectedRows() {
		return mysql_affected_rows($this->conn);
	}

	public function getInsertId() {
		return mysql_insert_id($this->conn);
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
		if(isset($this->result)) {
			if(is_resource($this->result)) mysql_free_result($this->result);
			unset($this->result);
		}
	}

	public function reset() {
		if(isset($this->result)) $this->freeResult();
	}

	/**
	 * Binds variables to a prepared statement as parameters
	 * @link http://www.php.net/manual/en/mysqli-stmt.bind-param.php
	 * @param types string <p>
	 * A string that contains one or more characters which specify the types
	 * for the corresponding bind variables:
	 * <table>
	 * Type specification chars
	 * <tr valign="top">
	 * <td>Character</td>
	 * <td>Description</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>i</td>
	 * <td>corresponding variable has type integer</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>d</td>
	 * <td>corresponding variable has type double</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>s</td>
	 * <td>corresponding variable has type string</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>b</td>
	 * <td>corresponding variable is a blob and will be sent in packets</td>
	 * </tr>
	 * </table>
	 * </p>
	 * @param var1 mixed <p>
	 * The number of variables and length of string
	 * types must match the parameters in the statement.
	 * </p>
	 * @param _ mixed[optional]
	 * @throws \mysqli_sql_exception
	 * @return \classes\db\mapper\mysqli\MysqliPrepareStmt
	 */
	public function bindParam() {
		$args = func_get_args();
		for($i=0; $i < strlen($args[0]); $i++) {
			$type = substr($args[0], $i, 1);
			$this->performCheckArguementType ( $type ,$i );

			$this->types[] = $type;
		}
		$this->bindParams = array_slice($args, 1);
		if(count($this->types) != count($this->bindParams) && count($this->types) && substr_count($this->query, '?')) throw new \InvalidArgumentException("not paired number parameter");

		for($i=0; $i < count($this->bindParams); $i++) {
			if($this->types[$i] == 'i') {
				if(!is_numeric($this->bindParams[$i])) throw new \InvalidArgumentException("truncated integer value");
				$this->bindParams[$i] = intval($this->bindParams[$i]);
			} elseif($this->types[$i] == 'd') {
				if(!is_numeric($this->bindParams[$i])) throw new \InvalidArgumentException("truncated double value");
				$this->bindParams[$i] = doubleval($this->bindParams[$i]);
			} else $this->bindParams[$i] = "'".mysql_real_escape_string($this->bindParams[$i], $this->conn)."'";
		}

		$this->performParameterMapping();

		return $this;
	}

	public function execute() {
		$this->result = mysql_query($this->query, $this->conn);
		$this->checkStmtError ();

		return $this->getInsertId() ? $this->getInsertId() : $this->getAffectedRows();
	}


	/**
	 * @throws \mysqli_sql_exception
	 */
	private function checkStmtError() {
		if ($this->getErrno()) throw new \mysqli_sql_exception($this->getError(), $this->getErrno());
	}

	public function fetchArray(array $iteratorMap = null) {
		$result = array ();

		$rows = $this->resourceFetchArray ();
		foreach ($rows as $row) {
			$fetchedRow = array ();

			foreach ($row as $fieldName => $value ) {
				if(is_null($iteratorMap) || !array_key_exists($fieldName, $iteratorMap)){
					array_push ( $fetchedRow, $value );
					$fetchedRow [$fieldName] = $value;
				} else {
					$relativeValue = call_user_func($iteratorMap[$fieldName]->closure, $value);
					array_push ( $fetchedRow, $relativeValue );
					$fetchedRow [$fieldName] = $relativeValue;
				}
			}

			array_push ( $result, $fetchedRow );
		}


		$this->freeResult();
		if(!$this->reusable) {
			$this->close();
		}
		else $this->reset();

		if(count($result) == 0 ) return null;
		return $result;
	}

	public function fetchObject($className = self::DEFAULT_CLASS_NAME, ObjectBuilderUtil $objectBuilder = null, array $iteratorMap = null) {
		$objectBuilder = $className != self::DEFAULT_CLASS_NAME ? new ObjectBuilderUtil ( $className ) : null;

		$rows = $this->resourceFetchArray ();

		$result = array ();

		if ($className == self::DEFAULT_CLASS_NAME) {
			foreach ($rows as $row) {
				$fetchedRow = new $className ();

				foreach ($row as $fieldName => $value ) {
					if(is_null($iteratorMap) || !array_key_exists($fieldName, $iteratorMap)){
						$fetchedRow->$fieldName = $value;
					} else {
						$fetchedRow->$fieldName = call_user_func($iteratorMap[$fieldName]->closure, $value);
					}
				}

				array_push ( $result, $fetchedRow );
			}
		} else {
			foreach ($rows as $row) {
				$fetchedRow = new $className ();
				$propertyList = array ();

				foreach ($row as $fieldName => $value ) {
					if(is_null($iteratorMap) || !array_key_exists($fieldName, $iteratorMap)){
						$propertyList [$fieldName] = $value;
					} else {
						$propertyList [$iteratorMap[$fieldName]->fieldName] = call_user_func($iteratorMap[$fieldName]->closure, $value);
					}
				}

				$fetchedRow = $objectBuilder->build ( $propertyList );
				unset ( $propertyList );
				array_push ( $result, $fetchedRow );
			}
		}

		$this->freeResult();
		if(!$this->reusable) {
			$this->close();
		}
		else $this->reset();

		if(count($result) == 0 ) return null;
		return $result;
	}

	public function executeAndFetch($className = self::DEFAULT_CLASS_NAME, $type = self::RESULT_OBJECT, array $iteratorMap = null) {
		try {
			$this->execute ();
		} catch (\Exception $ex) {
			throw $ex;
		}

		switch ($type) {
			case self::RESULT_OBJECT :
				$objectBuilder = $className != self::DEFAULT_CLASS_NAME ? new ObjectBuilderUtil ( $className ) : null;
				return $this->fetchObject ( $className, $objectBuilder, $iteratorMap);
			case self::RESULT_ARRAY: return $this->fetchArray ($iteratorMap);
			default: throw new \InvalidArgumentException('Invalid Return Type');
		}

		$this->stmt->free_result ();
		if(!$this->reusable) {
			$this->stmt->close();
			unset($this->stmt);
		}
		else $this->stmt->reset();
	}

	public function getQuery() {
		return $this->query;
	}

	private function performParameterMapping() {
		$query = $this->query;
		$args = $this->bindParams;
		$argsCount = count($args);

		$strlen = strlen($query);
		$offset = 0;

		while($offset != $argsCount) {
			$position = strpos($query, '?');
			if($position === false) {
				break;
			}

			$query = substr($query, 0, ($position)).$args[$offset++].substr($query, $position+1, $strlen);
		}

		$this->query = $query;
	}

	private function resourceFetchArray() {
		$rows = array();
		$row = mysql_fetch_array($this->result);
		while($row) {
			$rows[] = $row;
			$row = mysql_fetch_array($this->result);
		}
		return $rows;
	}

	/**
	 * @param string $type
	 * @throws \InvalidArgumentException
	 */
	private function performCheckArguementType($type, $extra = '') {
		switch ($type) {
			case 'i': break;
			case 'd': break;
 			case 's': break;
 			case 'b': break;
 			default: throw new \InvalidArgumentException('invalid type arguement '.$extra);
 		}
	}
}