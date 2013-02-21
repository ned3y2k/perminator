<?php
namespace component\gnuboard\repository;
use component\gnuboard\pool\MysqliPool;

class MysqliRepository {
	protected $connection;
	public function __construct() {
		$this->connection = MysqliPool::getInstance();
	}
}