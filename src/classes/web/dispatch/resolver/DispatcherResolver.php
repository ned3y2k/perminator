<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2015-02-26
 * Time: 오전 12:04
 */

namespace classes\web\dispatch\resolver;

use ApplicationContextPool;
use classes\web\dispatch\factory\ControllerFactory;
use classes\web\dispatch\factory\ResponseFactory;
use classes\web\dispatch\interceptor\InterceptorManager;
use classes\web\dispatch\loader\SimpleControllerLoader;
use classes\web\dispatch\resolver\clazz\IControllerClassNameResolver;
use classes\web\IInterceptorFinder;
use classes\web\mvc\ExceptionHandledController;
use classes\web\mvc\IController;


/**
 * Class DispatchResolver
 *
 * @package classes\web\dispatch
 */
class DispatcherResolver implements IDispatcherResolver {
	/** @var IControllerClassNameResolver */
	private $controllerClassNameResolver;
	private $controllerFactory;
	private $applicationContext;
	private $responseFactory;
	private $interceptorManager;

	/**
	 * DispatchResolver constructor.
	 *
	 * @param IControllerClassNameResolver $controllerClassNameResolver
	 *
	 * @throws \Exception
	 */
	public function __construct(IControllerClassNameResolver $controllerClassNameResolver) {
		$this->applicationContext = ApplicationContextPool::get();

		$this->controllerClassNameResolver = $controllerClassNameResolver;
		$this->controllerFactory = new ControllerFactory();
		$this->interceptorManager = new InterceptorManager();
		$this->responseFactory = new ResponseFactory($this->applicationContext, $this->interceptorManager);
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
		list($fullName, $cacheName) = $this->controllerClassNameResolver->resolve($className);

		try {
			$controller = $this->controllerFactory->createControllerInstance($fullName, $this->applicationContext);
			$this->sendResponse($controller);
		} catch (\Throwable $ex) {
			if (in_array('classes\web\mvc\ExceptionHandledController', class_implements($fullName))) {
				/** @var $fullName ExceptionHandledController */
				$fullName::handleException($ex);
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