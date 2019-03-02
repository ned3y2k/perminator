<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 4:47
 */

namespace classes\initializer;

use classes\web\dispatch\resolver\clazz\RouteEntryControllerNameResolver;
use classes\web\dispatch\resolver\DispatcherResolver;
use classes\web\dispatch\resolver\TestDispatcherResolver;
use DispatcherPool;

require_once __PERMINATOR__ . 'classes/pool/DispatcherPool.php';

class DispatcherInitializer implements Initializer {
	/**
	 * @throws \Exception
	 */
	public function init() {
		$controllerClassNameResolver = new RouteEntryControllerNameResolver();

		$dispatchResolver = !TEST
			? new DispatcherResolver($controllerClassNameResolver)
			: new TestDispatcherResolver($controllerClassNameResolver);

		DispatcherPool::init($dispatchResolver);
	}
}