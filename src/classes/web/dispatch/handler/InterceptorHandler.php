<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오후 1:15
 */

namespace classes\web\dispatch\handler;

use classes\web\{
	IHandlerInterceptor,
	mvc\IController,
	response\HttpResponse
};

class InterceptorHandler {
	/**
	 * @param IController           $controller
	 * @param IHandlerInterceptor[] $foundInterceptors
	 *
	 * @return HttpResponse|null
	 * @throws \Exception
	 */
	public function preHandles(IController $controller, array $foundInterceptors = null): ?HttpResponse {
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