<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오후 12:45
 */

namespace classes\web\dispatch\factory;


use classes\{
	context\IApplicationContext,
	web\dispatch\handler\InterceptorHandler,
	web\dispatch\handler\PostHandler,
	web\dispatch\interceptor\InterceptorManager,
	web\mvc\IController,
	web\response\HttpResponse
};

class DispatcherResponseFactory {
	/**
	 * @var InterceptorManager
	 */
	private $interceptorManager;
	private $interceptorHandler;
	private $postHandler;
	/**
	 * @var IApplicationContext
	 */
	private $applicationContext;


	/**
	 * ResponseFactory constructor.
	 *
	 * @param IApplicationContext $applicationContext
	 * @param InterceptorManager  $interceptorManager
	 */
	public function __construct(IApplicationContext $applicationContext, InterceptorManager $interceptorManager) {
		$this->applicationContext = $applicationContext;
		$this->interceptorManager = $interceptorManager;
		$this->postHandler = new PostHandler($this->applicationContext);
		$this->interceptorHandler = new InterceptorHandler();
	}

	/**
	 * @param IController $controller
	 *
	 * @return HttpResponse
	 * @throws \Exception
	 */
	public function create(IController $controller): HttpResponse {
		$foundInterceptors = $this->interceptorManager->findInterceptors();
		$response = $this->interceptorHandler->preHandles($controller, $foundInterceptors);

		if($response)
			return $response;

		$response = $controller->handleRequest();

		$this
			->postHandler
			->handle($foundInterceptors, $controller, $response)
			->send();

		return $response;
	}
}