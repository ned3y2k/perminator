<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-06
 * Time: 오후 5:16
 */

namespace classes\web\dispatch\handler;


use classes\context\IApplicationContext;
use classes\web\HttpResponse;
use classes\web\IHandlerInterceptor;
use classes\web\mvc\IController;
use classes\web\mvc\IPageBuilder;
use classes\web\mvc\PageBuilder;

class PostHandler {
	/**
	 * @var IApplicationContext
	 */
	private $applicationContext;


	public function __construct(IApplicationContext $applicationContext) {
		$this->applicationContext = $applicationContext;
	}

	/**
	 * @param             $postHandledInterceptors
	 * @param IController $controller
	 * @param             $response
	 *
	 * @return HttpResponse
	 * @throws \Exception
	 */
	public function handle($postHandledInterceptors, IController $controller, $response = null) {
		if ($response === null) {
			throw new \InvalidArgumentException(get_class($controller) . " handleRequest result is null");
		}

		if ($response instanceof PageBuilder) {
			getApplicationContext()->getSharedUserContext()->putSharedValue('pageBuilder', $response);
			return $this->performPageBuilderDisplay($postHandledInterceptors, $controller, $response);
		} elseif ($response instanceof HttpResponse) {
			$this->performResponseSend($this->applicationContext, $postHandledInterceptors, $controller, $response);

			return $response;
		} else {
			throw new \InvalidArgumentException("Invalid " . get_class($controller) . " handleRequest result. " . var_export($response, true));
		}
	}

	/**
	 * @param IApplicationContext   $applicationContext
	 * @param IHandlerInterceptor[] $postHandledInterceptors
	 * @param IController           $controller
	 * @param HttpResponse          $response
	 *
	 * @throws \Exception
	 */
	private function performResponseSend($applicationContext, $postHandledInterceptors, $controller, $response) {
		$sharedUserContext = $applicationContext->getSharedUserContext();
		foreach ($postHandledInterceptors as $postHandledInterceptor) {
			$results = $postHandledInterceptor->postHandle($controller);

			if ($results)
				foreach ($results as $key => $result) {
					$sharedUserContext->putSharedValue($key, $result);
				}
		}
	}

	/**
	 * @param IHandlerInterceptor[] $postHandledInterceptors
	 * @param IController           $controller
	 * @param IPageBuilder          $pageBuilder
	 *
	 * @return HttpResponse
	 * @throws \Exception
	 */
	private function performPageBuilderDisplay($postHandledInterceptors, IController $controller, IPageBuilder $pageBuilder) {
		foreach ($postHandledInterceptors as $postHandledInterceptor) {
			$results = $postHandledInterceptor->postHandle($controller);

			if ($results)
				foreach ($results as $key => $result) {
					$pageBuilder->putContent($key, $result);
				}
		}

		return $pageBuilder->display();
	}
}