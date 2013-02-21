<?php
namespace component\gnuboard\pool;
use conf\GNUDBConfig;
use conf\GNUConfig;

class GNUConfigPool {
	/**
	 * @return \conf\GNUConfig
	 */
	public static function getConfig() {
		static $instance = null;
		if ($instance == null) {
			$instance = new GNUConfig();
		}
		return $instance;
	}

	/**
	 * @return \conf\GNUDBConfig
	 */
	public static function getDBConfig() {
		static $instance = null;
		if ($instance == null) {
			$instance = new GNUDBConfig();
		}
		return $instance;
	}
}