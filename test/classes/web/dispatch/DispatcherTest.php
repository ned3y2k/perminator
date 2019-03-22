<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오후 5:42
 */

namespace classes\web\dispatch;


use classes\test\BitTestCase;
use classes\web\dispatch\executor\RequestExecutor;
use classes\web\dispatch\resolver\clazz\RouteEntryControllerNameResolver;
use classes\web\dispatch\resolver\DispatcherResolver;

class DispatcherTest extends BitTestCase {
	/** @var Dispatcher */
	private $dispatcher;

	/**
	 * @throws \Exception
	 */
	protected function setUp() {
		$controllerClassNameResolver = new RouteEntryControllerNameResolver();
		$dispatcherResolver = new DispatcherResolver($controllerClassNameResolver);
		$requestExecutor = new RequestExecutor();
		$this->dispatcher = new Dispatcher($dispatcherResolver, $requestExecutor);
	}


	/**
	 * @throws \Exception
	 */
	public function testDoDispatch() {
		$this->dispatcher->doDispatch('index');
	}
}
