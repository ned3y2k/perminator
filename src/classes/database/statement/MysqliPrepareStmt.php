<?php
namespace classes\database\statement;

use classes\database\IDatabaseStatement;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\util\object\ObjectBuilderUtil;

class MysqliPrepareStmt implements IDatabaseStatement {
	public $reusable = false;

	/** @var \mysqli */
	private $connection;
	private $affected_rows;
	private $insert_id;
	private $num_rows;
	private $param_count;
	private $field_count;
	private $errno;
	private $error;
	private $sqlstate;
	private $id;

	/** @var \mysqli_stmt */
	private $stmt;
	/** @var string */
	private $query;

	public final function getAffectedRows() { return $this->stmt->affected_rows; }

	public final function getInsertId() { return $this->stmt->insert_id; }

	public final function getNumRows() { return $this->stmt->num_rows; }

	public final function getParamCount() { return $this->stmt->param_count; }

	public final function getFieldCount() { return $this->stmt->field_count; }

	public final function getErrno() { return $this->stmt->errno; }

	public final function getError() { return $this->stmt->error; }

	public final function getSqlstate() { return $this->stmt->sqlstate; }

	public final function getId() { return $this->stmt->id; }

	/**
	 * @param \mysqli_stmt $stmt
	 * @param \mysqli      $connection
	 * @param bool         $reusable
	 */
	public function __construct(\mysqli_stmt $stmt, \mysqli $connection, $reusable = false) {
		$this->stmt     = $stmt;
		$this->connection = $connection;
		$this->reusable = $reusable;
	}

	/**
	 * @param \mysqli $mysqli
	 * @param string  $query
	 * @param bool    $reusable
	 *
	 * @throws \mysqli_sql_exception
	 * @throws \InvalidArgumentException
	 * @return MysqliPrepareStmt
	 */
	public static function prepare($mysqli, $query, $reusable = false) {
		if (!($mysqli instanceof \mysqli)) throw new \InvalidArgumentException();

		$stmt = $mysqli->prepare($query);
		if ($stmt == false) throw new \mysqli_sql_exception($mysqli->error, $mysqli->errno);

		$self = new self($stmt, $mysqli, $reusable);
		$self->query = $query;
		return $self;
	}

	/**
	 * Used to getParent the current value of a statement attribute
	 * @link http://www.php.net/manual/en/mysqli-stmt.attr-getParent.php
	 *
	 * @param int $attr
	 *
	 * @internal param int $attr <p>
	 * The attribute that you want to getParent.
	 * </p>
	 * @return int false if the attribute is not found, otherwise returns the value of the attribute.
	 */
	public function attrGet($attr) { return $this->stmt->attr_get($attr); }

	/**
	 * Used to modify the behavior of a prepared statement
	 * @link http://www.php.net/manual/en/mysqli-stmt.attr-set.php
	 *
	 * @param int $attr
	 * @param int $mode
	 *
	 * @internal param int $attr <p>
	 * The attribute that you want to set. It can have one of the following values:
	 * <table>
	 * Attribute values
	 * <tr valign="top">
	 * <td>Character</td>
	 * <td>Description</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MYSQLI_STMT_ATTR_UPDATE_MAX_LENGTH</td>
	 * <td>
	 * If set to 1, causes mysqli_stmt_store_result to
	 * update the metadata MYSQL_FIELD->max_length value.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MYSQLI_STMT_ATTR_CURSOR_TYPE</td>
	 * <td>
	 * Type of cursor to open for statement when mysqli_stmt_execute
	 * is invoked. mode can be MYSQLI_CURSOR_TYPE_NO_CURSOR
	 * (the default) or MYSQLI_CURSOR_TYPE_READ_ONLY.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MYSQLI_STMT_ATTR_PREFETCH_ROWS</td>
	 * <td>
	 * Number of rows to fetch from server at a time when using a cursor.
	 * mode can be in the range from 1 to the maximum
	 * value of unsigned long. The default is 1.
	 * </td>
	 * </tr>
	 * </table>
	 * </p>
	 * <p>
	 * If you use the MYSQLI_STMT_ATTR_CURSOR_TYPE option with
	 * MYSQLI_CURSOR_TYPE_READ_ONLY, a cursor is opened for the
	 * statement when you invoke mysqli_stmt_execute. If there
	 * is already an open cursor from a previous mysqli_stmt_execute call,
	 * it closes the cursor before opening a new one. mysqli_stmt_reset
	 * also closes any open cursor before preparing the statement for re-execution.
	 * mysqli_stmt_free_result closes any open cursor.
	 * </p>
	 * <p>
	 * If you open a cursor for a prepared statement, mysqli_stmt_store_result
	 * is unnecessary.
	 * </p>
	 * @internal param int $mode <p>The value to assign to the attribute.</p>
	 * @return bool
	 */
	public function attrSet($attr, $mode) {
		return $this->stmt->attr_set($attr, $mode);
	}

	/**
	 * Closes a prepared statement
	 * @link http://www.php.net/manual/en/mysqli-stmt.close.php
	 * @throws \mysqli_sql_exception
	 * @return MysqliPrepareStmt
	 */
	public function close() {
		if (!$this->stmt->close()) throw new \mysqli_sql_exception('close call fail');

		return $this;
	}

	/**
	 * Seeks to an arbitrary row in statement result set
	 * @link http://www.php.net/manual/en/mysqli-stmt.data-seek.php
	 *
	 * @param int $offset
	 *
	 * @internal param int $offset <p>
	 * Must be between zero and the total number of rows minus one (0..
	 * mysqli_stmt_num_rows - 1).
	 * </p>
	 * @return MysqliPrepareStmt
	 */
	public function dataSeek($offset) {
		$this->stmt->data_seek($offset);

		return $this;
	}

	/**
	 * Frees stored result memory for the given statement handle
	 * @link http://www.php.net/manual/en/mysqli-stmt.free-result.php
	 * @return MysqliPrepareStmt
	 */
	public function freeResult() {
		$this->stmt->free_result();

		return $this;
	}

	/**
	 * Resets a prepared statement
	 * @link http://www.php.net/manual/en/mysqli-stmt.reset.php
	 * @throws \mysqli_sql_exception
	 * @return MysqliPrepareStmt
	 */
	public function reset(): MysqliPrepareStmt {
		if (!$this->stmt->reset()) throw new \mysqli_sql_exception('reset call fail');
		return $this;
	}

	/**
	 * Transfers a result set from a prepared statement
	 * @link http://www.php.net/manual/en/mysqli-stmt.store-result.php
	 * @throws \mysqli_sql_exception
	 * @return MysqliPrepareStmt
	 */
	public function storeResult() {
		if (!$this->stmt->store_result()) throw new \mysqli_sql_exception('storeResult call fail');

		return $this;
	}

	/**
	 * Binds variables to a prepared statement as parameters
	 * @link http://www.php.net/manual/en/mysqli-stmt.bind-param.php
	 *
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
	 *
	 * @throws \mysqli_sql_exception
	 * @return MysqliPrepareStmt
	 */
	public function bindParam() {
		/** @noinspection PhpUnusedLocalVariableInspection */
		$stmt      = $this->stmt;
		$argString = "";

		if(func_num_args() != 0) {
			//$arr = func_get_args(); call_user_func_array ( array ( $this->stmt, "bind_param" ), &$arr); // 요놈이 아래처럼 수정됨
			foreach (func_get_args() as $arg) {
				$arg = str_replace('\\','\\\\', $arg);
				$argString .= ",'" . str_replace("'", "\\'", $arg) . "'";
			}

			eval ('call_user_func(array($stmt, "bind_param"),' . substr($argString, 1) . ');');
		}

		$this->checkStmtError();

		return $this;
	}

	/**
	 * Executes a prepared Query
	 * @link http://www.php.net/manual/en/mysqli-stmt.execute.php
	 * @throws \mysqli_sql_exception
	 * @return bool Returns true on success or false on failure.
	 */
	public function execute() {
		$this->stmt->execute();
		$this->checkStmtError();

		return $this->getInsertId() ? $this->getInsertId() : $this->getAffectedRows();
	}

	/**
	 * @throws \mysqli_sql_exception
	 */
	private function checkStmtError() { if ($this->stmt->errno) throw new \mysqli_sql_exception($this->stmt->error, $this->stmt->errno); }

	public function fetchArray(array $iteratorMap = null) {
		$stmt = $this->stmt;

		$fields     = $stmt->result_metadata()->fetch_fields();
		$fieldCount = count($fields);

		$bindMap    = array();
		$bindString = "";
		for ($i = 0; $i < $fieldCount; $i++) {
			array_push($bindMap, null);
			$bindString .= ",\$bindMap[$i]";
		}
		eval ('$stmt->bind_result(' . substr($bindString, 1) . ');');

		$stmt->store_result();

		$result = array();
		while ($stmt->fetch()) {
			$fetchedRow = array();
			for ($i = 0; $i < $fieldCount; $i++) {
				if ($iteratorMap == null || !array_key_exists($fields [ $i ]->name, $iteratorMap)) {
					array_push($fetchedRow, $bindMap [ $i ]);
					$fetchedRow [ $fields [ $i ]->name ] = $bindMap [ $i ];
				} else {
					$relativeValue = call_user_func($iteratorMap[ $fields [ $i ]->name ]->closure, $bindMap [ $i ]);
					array_push($fetchedRow, $relativeValue);
					$fetchedRow [ $fields [ $i ]->name ] = $relativeValue;
				}
			}

			array_push($result, $fetchedRow);
		}

		$stmt->free_result();
		if (!$this->reusable) {
			$this->stmt->close();
			$this->stmt = null;
		} else $this->stmt->reset();

		$stmt = null;

		if (count($result) == 0) return null;

		return $result;
	}

	/**
	 * @param string $className
	 * @param ObjectBuilderUtil|null $objectBuilder
	 * @param array|null $iteratorMap
	 * @return array|null
	 * @throws \ReflectionException
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function fetchObject($className = self::DEFAULT_CLASS_NAME, ObjectBuilderUtil $objectBuilder = null, array $iteratorMap = null) {
		$objectBuilder = $className != self::DEFAULT_CLASS_NAME ? new ObjectBuilderUtil ($className) : null;
		$stmt          = $this->stmt;

		$resultMetaData = $stmt->result_metadata();
		if(!$resultMetaData) {
			throw new \InvalidArgumentException('not has field. may procedure sql executed.'.$this->query);
		}

		$fields = $resultMetaData->fetch_fields();

		$fieldCount    = count($fields);

		$bindMap    = array();
		$bindString = "";
		for ($i = 0; $i < $fieldCount; $i++) {
			array_push($bindMap, null);
			$bindString .= ",\$bindMap[$i]";
		}


		/* @var $reflectionMethod \ReflectionMethod
		$reflectionClass = new \ReflectionClass ( '\mysqli_stmt' );
		 * $reflectionMethod = $reflectionClass->getMethod ( 'bind_result' );
		 * unset ( $reflectionClass );
		 * $reflectionMethod->invokeArgs ( $this->stmt, $bindMap );
		 * unset ( $reflectionMethod ); */

		eval ('$stmt->bind_result(' . substr($bindString, 1) . ');');

		$stmt->store_result();
		$result = array();

		if ($className == self::DEFAULT_CLASS_NAME) {
			while ($stmt->fetch()) {
				$fetchedRow = new $className ();
				for ($i = 0; $i < $fieldCount; $i++) {
					$fieldName = $fields [$i]->name;

					if ($iteratorMap == null || !array_key_exists($fields [ $i ]->name, $iteratorMap)) {
						$fetchedRow->$fieldName = $bindMap [ $i ];
					} else {
						$fetchedRow->$fieldName = call_user_func($iteratorMap[ $fields [ $i ]->name ]->closure, $bindMap [ $i ]);
					}
				}

				array_push($result, $fetchedRow);
			}
		} else {
			while ($stmt->fetch()) {
				$propertyList = array();
				for ($i = 0; $i < $fieldCount; $i++) {
					if ($iteratorMap == null || !array_key_exists($fields [ $i ]->name, $iteratorMap)) {
						$propertyList [ $fields [ $i ]->name ] = $bindMap [ $i ];
					} else {
						$propertyList [ $iteratorMap[ $fields [ $i ]->name ]->fieldName ] = call_user_func($iteratorMap[ $fields [ $i ]->name ]->closure, $bindMap [ $i ]);
					}
				}

				$fetchedRow = $objectBuilder->build($propertyList);
				unset ($propertyList);
				array_push($result, $fetchedRow);
			}
		}

		$stmt->free_result();
		if (!$this->reusable) {
			$this->stmt->close();
			unset($this->stmt);
		} else $this->stmt->reset();

		unset ($stmt);

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
	function executeAndFetch($className = self::DEFAULT_CLASS_NAME, $type = self::RESULT_OBJECT, array $iteratorMap = null) {
		try {
			$this->execute();
		} catch (\Exception $ex) {
			throw $ex;
		}

		$result = null;
		switch ($type) {
			case self::RESULT_OBJECT :
				$objectBuilder = $className != self::DEFAULT_CLASS_NAME ? new ObjectBuilderUtil ($className) : null;
				$result        = $this->fetchObject($className, $objectBuilder, $iteratorMap);
				break;
			case self::RESULT_ARRAY:
				$result = $this->fetchArray($iteratorMap);
				break;
			default:
				throw new \InvalidArgumentException('Invalid Return Type');
		}

		return $result;
	}

	function escapeString($string) {
		return $this->connection->real_escape_string($string);
	}
}
