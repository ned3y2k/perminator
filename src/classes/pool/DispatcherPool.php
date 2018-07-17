<?php

use classes\{
	web\dispatch\Dispatcher,
	web\dispatch\executor\RequestExecutor,
	web\dispatch\executor\TestRequestExecutor,
	web\dispatch\resolver\IDispatcherResolver,
	web\HttpResponse
};

/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-03
 * Time: 오후 2:40
 */
class DispatcherPool {
	/** @var Dispatcher */
	private static $instance = null;

	public static function init(IDispatcherResolver $dispatchResolver) {
		$executor = TEST ? new TestRequestExecutor() : new RequestExecutor();

		self::$instance = new Dispatcher($dispatchResolver, $executor);
	}

	/** @return Dispatcher */
	public static function get() { return self::$instance; }
}

/**
 * @param null $className
 *
 * @return HttpResponse
 * @throws \Exception
 */
function doDispatch($className = null): HttpResponse {
	$res = DispatcherPool::get()->doDispatch($className);

	if (TEST && empty($className)) {
		throw new \InvalidArgumentException("class name null");
	}

	return $res;
}