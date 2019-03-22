<?php
namespace classes\database;

use classes\database\statement\MysqliPrepareStmt;
use classes\database\statement\MysqlPrepareStmt;
use classes\database\statement\PDOPrepareStatement;

class DatabaseStatementBuilder {
	private $connection;
	/** @var bool */
	private $reusable = false;
	/** @var string */
	private $query;
	/** @var string */
	private $dbType;

	private function __construct() { }

	/**
	 * @param mixed $connection
	 *
	 * @return DatabaseStatementBuilder
	 */
	public function setConnection($connection) {
		$this->connection = $connection;

		return $this;
	}

	/**
	 * @param bool $reusable
	 *
	 * @return DatabaseStatementBuilder
	 */
	public function setReusable($reusable) {
		$this->reusable = $reusable;

		return $this;
	}

	/**
	 * @param string $query
	 *
	 * @return DatabaseStatementBuilder
	 */
	public function setQuery($query) {
		$this->query = $query;

		return $this;
	}

	/**
	 * @param string $dbType
	 *
	 * @return DatabaseStatementBuilder
	 */
	public function setDBType($dbType) {
		$this->dbType = $dbType;

		return $this;
	}

	/**
	 * @return DatabaseStatementBuilder
	 */
	static function builder() {
		return new DatabaseStatementBuilder();
	}

	/** @return MysqliPrepareStmt|MysqlPrepareStmt|PDOPrepareStatement */
	function build() {
		if ($this->dbType === null) throw new \InvalidArgumentException ("DBType was empty");
		if ($this->query === null) throw new \InvalidArgumentException ("Query was empty");
		if ($this->connection === null) throw new \InvalidArgumentException ("Connection was empty");

		if ($this->dbType == 'mysql') {
			return MysqlPrepareStmt::prepare($this->connection, $this->query, $this->reusable);
		} elseif ($this->dbType == 'mysqli') {
			return MysqliPrepareStmt::prepare($this->connection, $this->query, $this->reusable);
		} elseif($this->dbType == 'pdo') {
			return PDOPrepareStatement::prepare($this->connection, $this->query, $this->reusable);
		}

		throw new \InvalidArgumentException ("Invalid DB Type");
	}
}