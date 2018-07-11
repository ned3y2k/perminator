<?php
/**
 * User: Kyeongdae
 * Date: 2017-05-17
 * Time: 오후 10:01
 */

namespace classes\database\statement;


use classes\database\IDatabaseStatement;
use classes\util\object\ObjectBuilderUtil;

class PDOPrepareStatement implements IDatabaseStatement {
	/** @var \PDO */
	private $connection;
	/** @var \PDOStatement */
	private $stmt;
	/** @var bool */
	private $reusable;

	static function prepare($connection, $stmt, $reusable = false) {
		if (!($connection instanceof \PDO)) {
			throw new \InvalidArgumentException('Invalid Connection Instance');

		}
		$connection->prepare($stmt);
		$connection->errorCode();

		return new self($connection, $connection->prepare($stmt), $reusable);
	}

	public function __construct(\PDO $connection, \PDOStatement $stmt, bool $reusable = false) {
		$this->connection = $connection;
		$this->stmt       = $stmt;
		$this->reusable   = $reusable;
	}

	function getAffectedRows() { return $this->stmt->rowCount(); }

	function getInsertId() { return $this->connection->lastInsertId(); }

	function getNumRows() { return $this->stmt->rowCount(); }

	function getErrno() { return $this->stmt->errorCode(); }

	function getError() { return $this->stmt->errorInfo(); }

	function getSqlstate() { return $this->stmt->errorCode(); }

	function close() { $this->stmt->closeCursor(); }

	function storeResult() {
		// TODO: Implement storeResult() method.
	}

	function bindParam() {
		$args = func_get_args();
		array_shift($args);
		foreach ($args as $key=>$val) {
			if(is_int($val)) {
				$this->stmt->bindValue($key + 1, $val, \PDO::PARAM_INT);
			} else {
				$this->stmt->bindValue($key + 1, $val);
			}

		}
		return $this;
	}

	function execute() {
		if(!$this->stmt->execute()) {
			$msg =  $this->stmt->errorCode().": ".var_export($this->stmt->errorInfo(), true);
			throw new PDOPrepareStatementException($msg, -1, null);
		}
	}

	function fetchArray(array $iteratorMap = null) {
		$stmt = $this->stmt;

		$rows = [];
		while ($row = $stmt->fetch()) {
			$fetchedRow = [];

			foreach ($row as $key => $val) {
				if (!$iteratorMap || !array_key_exists($key, $iteratorMap)) {
					$fetchedRow [$key] = $val;
				} else {
					$relativeValue     = call_user_func($iteratorMap[$key]->closure, $val);
					$fetchedRow [$key] = $relativeValue;
				}
			}

			$rows[] = $fetchedRow;
		}

		if (!$this->reusable) {
			$this->stmt->closeCursor();
			$this->stmt = null;
		}

		return !$rows ? null : $rows;
	}

	function fetchObject($className = self::DEFAULT_CLASS_NAME, ObjectBuilderUtil $objectBuilder = null, array $iteratorMap = null) {
		$rows = [];
		$stmt = $this->stmt;

		if($className == self::DEFAULT_CLASS_NAME) {
			while ($row = $stmt->fetchObject()) {
				$fetchedRow = new \stdClass();

				foreach ($row as $key => $val) {
					if (!$iteratorMap || !array_key_exists($key, $iteratorMap)) {
						$fetchedRow->{$key} = $val;
					} else {
						$relativeValue     = call_user_func($iteratorMap[$key]->closure, $val);
						$fetchedRow->{$key} = $relativeValue;
					}
				}

				$rows[] = $row;
			}
		} else {
			while ($row = $stmt->fetch()) {
				$propertyList = array();
				foreach ($row as $key => $val) {
					if ($iteratorMap == null || !array_key_exists($key, $iteratorMap)) {
						$propertyList [ $key ] = $val;
					} else {
						$propertyList [ $iteratorMap[ $key ]->fieldName ] = call_user_func($iteratorMap[ $key ]->closure, $val);
					}
				}

				$fetchedRow = $objectBuilder->build($propertyList);
				unset ($propertyList);
				$rows[] = $fetchedRow;
			}
		}


		if (!$this->reusable) {
			$this->stmt->closeCursor();
			$this->stmt = null;
		}

		return !$rows ? null : $rows;
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

	function escapeString($string) { return $this->connection->quote($string); }


	function dataSeek($offset) { unsupportedOperation(); }

	function freeResult() { unsupportedOperation(); }

	function reset() { unsupportedOperation(); }

}