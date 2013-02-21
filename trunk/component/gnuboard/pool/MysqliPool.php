<?php
namespace component\gnuboard\pool;
use conf\GNUDBConfig;

class MysqliPool {
	/**
	 *
	 * @return \mysqli
	 */
	public static function getInstance() {
		static $instance = null;
		if ($instance == null) {
			$instance = new \mysqli ( GNUDBConfig::MYSQL_HOST, GNUDBConfig::MYSQL_USER, GNUDBConfig::MYSQL_PASSWORD );
			$check = $instance->select_db ( GNUDBConfig::MYSQL_DB );
			if (! $check)
				throw new \RuntimeException ( 'Permission Denied' );
		}
		return $instance;
	}
}
