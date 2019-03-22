<?php
namespace classes\database;

use classes\database\statement\MysqliPrepareStmt;

class Database {
	private $connection;
	private $type;

	public function __construct($connection, $type = null) {
		if (!is_object($connection) && $type === null) throw new \InvalidArgumentException("Resource type has a type parameter is required.");

		$this->connection = $connection;
		$this->type       = $type;
	}

	/**
	 * @param string $query
	 *
	 * @return \classes\database\IDatabaseStatement
	 */
	public function prepare($query) { // FIXME Resource Type인지 Object Type인지에 따라 자동으로 바뀌는건데 요렇게 쓰면 더 많은 DB를 허용할수 없다. 오직 mysql만 가능하니 수정 필요
		if ($this->type === null) {
			return $this->resourceTypePreparedStatement($query);
		} else {
			return $this->objectTypePreparedStatement($query);
		}
	}

	/**
	 * @param string $query
	 *
	 * @return void
	 */
	private function resourceTypePreparedStatement($query) {
		switch ($this->type) {
			case "mysql":
				return;
			default:
				throw new \InvalidArgumentException("Invalid DB Type");
		}
	}

	/**
	 * @param string $query
	 *
	 * @throws \mysqli_sql_exception
	 * @return \classes\database\IDatabaseStatement
	 */
	private function objectTypePreparedStatement($query) {
		if ($this->connection instanceof \mysqli) return MysqliPrepareStmt::prepare($this->connection, $query);
		throw new \mysqli_sql_exception('DB에 연결할수 없습니다.');
	}
}