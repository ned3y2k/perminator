<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-12
 * Time: 오후 5:40
 */

namespace classes\web\dispatch\executor;


use classes\context\IApplicationContext;
use classes\io\exception\FileNotFoundException;
use classes\web\dispatch\factory\ControllerFactory;
use classes\web\dispatch\resolver\clazz\IControllerClassNameResolver;
use classes\web\HttpResponse;
use classes\web\mvc\IPageBuilder;

class ControllerExecutor {
	private $nameResolver;
	private $factory;
	private $context;

	public function __construct(IApplicationContext $context, IControllerClassNameResolver $nameResolver) {
		$this->factory = new ControllerFactory();
		$this->nameResolver = $nameResolver;
		$this->context = $context;
	}

	public function resolveName($providedControllerClassName) {
		return $this->nameResolver->resolve($providedControllerClassName);
	}

	/**
	 * @param $controllerName
	 * @return HttpResponse|IPageBuilder
	 * @throws FileNotFoundException
	 */
	public function execute($controllerName) {
		$controller = $this->factory->createControllerInstance($controllerName, $this->context);
		$controller->setApplicationContext($this->context);
		return $controller->handleRequest();
	}
}