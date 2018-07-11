<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오후 12:45
 */

namespace classes\web\dispatch\factory;


use classes\context\IApplicationContext;
use classes\web\dispatch\handler\InterceptorHandler;
use classes\web\dispatch\handler\PostHandler;
use classes\web\dispatch\interceptor\InterceptorManager;
use classes\web\HttpResponse;
use classes\web\mvc\IController;
use classes\web\mvc\PageBuilder;

class ResponseFactory {
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
		$response = $this->interceptorHandler->preHandles($foundInterceptors, $controller);

		if($response)
			return $response;

		$response = $controller->handleRequest();

		$this
			->postHandler
			->handle($foundInterceptors, $controller, $response)
			->send();

		return $response;
	}


	/** @param PageBuilder|HttpResponse $response */
	private function displayPreHandleResponse($response) {
		if ($response instanceof PageBuilder) {
			$response->display();
		} elseif ($response instanceof HttpResponse) {
			$response->send();
		} else {
			throw new \InvalidArgumentException('Ambiguous response type. Allow only \classes\web\mvc\PageBuilder or \classes\web\HttpResponse');
		}
	}
}