<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2015-02-26
 * Time: 오전 12:04
 */

namespace classes\web\dispatch\resolver;

use ApplicationContextPool;
use classes\web\dispatch\executor\ControllerExecutor;
use classes\web\dispatch\factory\ResponseFactory;
use classes\web\dispatch\interceptor\InterceptorManager;
use classes\web\dispatch\resolver\clazz\IControllerClassNameResolver;
use classes\web\IInterceptorFinder;
use classes\web\mvc\IExceptionHandledController;
use classes\web\mvc\IController;


/**
 * Class DispatchResolver
 *
 * @package classes\web\dispatch
 */
class DispatcherResolver implements IDispatcherResolver {
	/** @var IControllerClassNameResolver */
	private $controllerClassNameResolver;
	private $applicationContext;
	private $responseFactory;
	private $interceptorManager;
	private $controllerExecutor;

	/**
	 * DispatchResolver constructor.
	 *
	 * @param IControllerClassNameResolver $controllerClassNameResolver
	 *
	 * @throws \Exception
	 */
	public function __construct(IControllerClassNameResolver $controllerClassNameResolver) {
		$this->applicationContext = ApplicationContextPool::get();

		$this->interceptorManager = new InterceptorManager();
		$this->responseFactory = new ResponseFactory($this->applicationContext, $this->interceptorManager);
		$this->controllerExecutor = new ControllerExecutor($this->applicationContext, $controllerClassNameResolver);
	}


	/**
	 * @param IInterceptorFinder $interceptorFinder
	 *
	 * @return void
	 */
	public function addInterceptorFinder(IInterceptorFinder $interceptorFinder) { $this->interceptorManager->addInterceptorFinder($interceptorFinder); }

	/**
	 * @param string $className
	 *
	 * @return mixed|void
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function resolve($className) {
		$fullName = $this->controllerClassNameResolver->resolve($className);

		try {
			$res = $this->controllerExecutor->execute($fullName);
		} catch (\Throwable $ex) {
			if (in_array('classes\web\mvc\IExceptionHandledController', class_implements($fullName, false))) {
				/** @var $fullName IExceptionHandledController */
				$fullName::handleException($ex)->send();
			} elseif (!TEST)
				$this->applicationContext->getExceptionHandler()->handling($ex);
			else throw $ex;
		}

	}


	/**
	 * @param IController $controller
	 *
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	private function sendResponse(IController $controller) {
		try {
			$this->responseFactory->create($controller);
		} catch (\Exception $e) {
//			$response = new HttpResponse();
//			$response->status(500);
//			$response->setBody($e);
//			$response->send();
			throw $e;
		}
	}
}