<?php
namespace component\database;
use component\database\statement\MysqliPrepareStmt;

class Database {
	private $connection;
	private $type;

	public function __construct($connection, $type = null) {
		if(!is_object($connection) && is_null($type)) throw new \InvalidArgumentException("Resource type has a type parameter is required.");

		$this->connection = $connection;
		$this->type = $type;
	}

	/**
	 * @return \classes\database\IDatabaseStatement
	 */
	public function prepare($query) {
		if(is_null($this->type)) {
			return $this->resoureTypePreparedStatement();
		} else {
			return $this->objectTypePreparedStatement();
		}
	}

	/**
	 * @param string $query
	 * @throws \InvalidArgumentException
	 * @return \classes\database\IDatabaseStatement
	 */
	private function resoureTypePreparedStatement($query) {
		switch ($this->type) {
			case "mysql":
				return ;
			default: throw new \InvalidArgumentException("Invalid DB Type");
		}
	}

	/**
	 * @param string $query
	 * @return \classes\database\IDatabaseStatement
	 */
	private function objectTypePreparedStatement($query) {
		if($this->connection instanceof \mysqli) return MysqliPrepareStmt::prepare($this->connection, $query);
	}
}