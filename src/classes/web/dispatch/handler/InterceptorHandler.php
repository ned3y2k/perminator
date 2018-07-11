<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오후 1:15
 */

namespace classes\web\dispatch\handler;


use classes\web\HttpResponse;
use classes\web\IHandlerInterceptor;
use classes\web\mvc\IController;

class InterceptorHandler {
	/**
	 * @param IHandlerInterceptor[] $foundInterceptors
	 * @param IController           $controller
	 *
	 * @return HttpResponse|null
	 * @throws \Exception
	 */
	public function preHandles(array $foundInterceptors = null, IController $controller): ?HttpResponse {
		if (!$foundInterceptors) {
			foreach ($foundInterceptors as $foundInterceptor) {
				$flag = $foundInterceptor->preHandle($controller, $response);

				if (!$flag) {
					return $response;
				}
			}
		}

		return null;
	}

	/**
	 * @param IHandlerInterceptor[] $foundInterceptors
	 * @param IController           $controller
	 *
	 * @throws \Exception
	 */
	public function postHandle(array $foundInterceptors, IController $controller) {
		if (!$foundInterceptors) {
			foreach ($foundInterceptors as $foundInterceptor) {
				$foundInterceptor->postHandle($controller);
			}
		}
	}
}