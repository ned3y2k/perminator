<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2015-02-26
 * Time: 오전 12:04
 */

namespace classes\web\dispatch\resolver;

use ApplicationContextPool;
use classes\{
	web\dispatch\executor\ControllerExecutor,
	web\dispatch\factory\DispatcherResponseFactory,
	web\dispatch\interceptor\InterceptorManager,
	web\dispatch\resolver\clazz\IControllerClassNameResolver,
	web\IInterceptorFinder,
	web\mvc\IController,
	web\mvc\IExceptionHandledController,
	web\response\HttpResponse
};


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
		$this->controllerClassNameResolver = $controllerClassNameResolver;
		$this->interceptorManager = new InterceptorManager();
		$this->responseFactory = new DispatcherResponseFactory($this->applicationContext, $this->interceptorManager);
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
	 * @return HttpResponse
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function resolve($className): HttpResponse {
		$fullName = $this->controllerClassNameResolver->resolve($className);

		try {
			$res = $this->controllerExecutor->execute($fullName);
			
			return $res;
		} catch (\Throwable $ex) {
			if (in_array('classes\web\mvc\IExceptionHandledController', class_implements($fullName, false))) {
				/** @var $fullName IExceptionHandledController */
				return $fullName::handleException($this->applicationContext, $ex);
			} else {
				$this->applicationContext->getExceptionHandler()->handling($ex);
				exit;
			}
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