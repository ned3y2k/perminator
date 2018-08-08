<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2015-02-26
 * Time: 오전 12:19
 */

namespace classes\web\dispatch\resolver;

use classes\{
	cache\CacheManagerPool,
	context\IApplicationContext,
	pool\LibraryLoaderPool,
	web\dispatch\factory\ControllerFactory,
	web\dispatch\handler\PostHandler,
	web\dispatch\resolver\clazz\IControllerClassNameResolver,
	web\IHandlerInterceptor,
	web\IInterceptorFinder,
	web\mvc\IController,
	web\mvc\IPageBuilder,
	web\mvc\PageBuilder,
	web\response\HttpResponse
};

/**
 * Class TestDispatchResolver
 *
 * @package classes\web\dispatch
 */
class TestDispatcherResolver implements IDispatcherResolver {
	/** @var IInterceptorFinder[] */
	private $interceptorFinders = array();

	/** @var IControllerClassNameResolver */
	private $controllerClassNameResolver;
	private $controllerFactory;
	private $postHandler;

	/**
	 * TestDispatchResolver constructor.
	 *
	 * @param IControllerClassNameResolver $controllerClassNameResolver
	 */
	public function __construct(IControllerClassNameResolver $controllerClassNameResolver) {
		$this->controllerClassNameResolver = $controllerClassNameResolver;
		$this->postHandler = new PostHandler(getApplicationContext());
		$this->controllerFactory = new ControllerFactory();
	}


	/**
	 * @param IInterceptorFinder $interceptorFinder
	 *
	 * @return void
	 */
	public function addInterceptorFinder(IInterceptorFinder $interceptorFinder) { $this->interceptorFinders[] = $interceptorFinder; }

	/**
	 * @param string $className
	 *
	 * @return HttpResponse|IPageBuilder|null|
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 * @throws \Throwable
	 */
	public function resolve($className) {
		if (is_string($className)) {
			list($fullName, $cacheName) = $this->controllerClassNameResolver->resolve($className);
		} elseif (is_object($className)) {
			/** @noinspection PhpParamsInspection */
			$fullName = get_class($className);
			$cacheName = "object-call-pathMap-" . $fullName;
		} else {
			throw new \UnsupportedOperationException("supported by object or stringClassName");
		}

		$applicationContext = getApplicationContext();

		$response = null;

		$controller = is_object($className)
			? $this->resolveLifeCycle($className, $applicationContext)
			: $this->controllerFactory->createControllerInstance($fullName, $applicationContext);

		try {
			echo "------------ begin send response ------------\n";
			$response = $this->sendResponse($controller, $this->interceptPreHandles($controller));
			$this->storeCacheLoadedLibrary($cacheName);
			echo "\n------------ end send response ------------\n";
		} catch (\Exception $ex) {
			if (!TEST)
				getApplicationContext()->getExceptionHandler()->handling($ex);
			else throw $ex;
		}

		return $response;
	}

	/**
	 * @param IController $controller
	 *
	 * @return IHandlerInterceptor[] postHandledInterceptors
	 * @throws \Exception
	 */
	private function interceptPreHandles($controller) {
		$foundInterceptors = $this->findInterceptors();
		$postHandledInterceptors = array();

		foreach ($foundInterceptors as $foundInterceptor) {
			$flag = $foundInterceptor->preHandle($controller, $response);

			if ($response != null)
				$this->displayPreHandleResponse($response);

			if ($flag)
				$postHandledInterceptors[] = $foundInterceptor;
		}

		return $postHandledInterceptors;
	}

	/** @return IHandlerInterceptor[] */
	private function findInterceptors() {
		$foundInterceptors = array();

		foreach ($this->interceptorFinders as $interceptorFinder) {
			$newFoundInterceptors = $interceptorFinder->findInterceptors($_SERVER['REQUEST_URI']);

			if (is_array($newFoundInterceptors) && count($newFoundInterceptors) != 0)
				$foundInterceptors = array_merge($foundInterceptors, $interceptorFinder->findInterceptors($_SERVER['URL']));
		}

		return $foundInterceptors;
	}

	/** @param PageBuilder|HttpResponse $response */
	private function displayPreHandleResponse($response) {
		if ($response instanceof PageBuilder) {
			$response->display(getApplicationContext()->getRequestContext());
		} elseif ($response instanceof HttpResponse) {
			$response->send();
		} else {
			throw new \InvalidArgumentException('Ambiguous response type. Allow only \classes\web\mvc\PageBuilder or \classes\web\HttpResponse');
		}
	}

	/**
	 * @param string $cacheName
	 *
	 * @throws \Exception
	 */
	private function storeCacheLoadedLibrary($cacheName) {
		if (LibraryLoaderPool::get()->getLoadedCount() != 0) {
			CacheManagerPool::getInstance()->delete($cacheName);
			CacheManagerPool::getInstance()->put($cacheName, LibraryLoaderPool::get()->getPathMap());
		}
	}

	/**
	 * @param IController           $controller
	 * @param IHandlerInterceptor[] $postHandledInterceptors
	 *
	 * @return HttpResponse
	 * @throws \InvalidArgumentException
	 * @throws \Exception
	 */
	private function sendResponse($controller, $postHandledInterceptors) {
		try {
			$response = $controller->handleRequest();

			return $this
				->postHandler
				->handle($postHandledInterceptors, $controller, $response);
		} catch (\Exception $e) {
//			$response = new HttpResponse();
//			$response->status(500);
//			$response->setBody($e);
//			$response->send();
			throw $e;
		}
	}

	/**
	 * @param IController         $controller
	 * @param IApplicationContext $context
	 *
	 * @return IController
	 */
	private function resolveLifeCycle(IController $controller, IApplicationContext $context) {
		$controller->setApplicationContext($context);
		return $controller;
	}
}