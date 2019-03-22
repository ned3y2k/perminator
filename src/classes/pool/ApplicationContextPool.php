<?php

use classes\context\IApplicationContext;

/**
 * User: Kyeongdae
 * Date: 2018-07-10
 * Time: 오전 10:53
 */
class ApplicationContextPool {
	private static $instance;

	public static function set(IApplicationContext $applicationContext) { self::$instance = $applicationContext; }

	public static function get(): IApplicationContext { return self::$instance; }

	public static function isInitialized(): bool {
		return true == self::$instance;
	}
}

/**
 * 공용 ApplicationContext
 *
 * @return IApplicationContext
 */
function getApplicationContext() {
	return ApplicationContextPool::get();
}
