<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 2014-12-23
 * 시간: 오후 5:38
 */

namespace classes\web;

use classes\web\mvc\IController;

interface IHandlerInterceptor {
	/**
	 * @param IController  $controller
	 * @param HttpResponse $response
	 *
	 * @throws \Exception
	 * @return bool
	 */
	function preHandle(IController $controller, &$response);

	/**
	 * Spring에서는 ModelAndView나 Model또는 View에 관여 하는듯
	 *
	 * @param IController $controller
	 *
	 * @throws \Exception
	 * @return array
	 */
	function postHandle(IController $controller);

	//function afterCompletion(HttpServletRequest request, HttpServletResponse response, Object handler, Exception ex) throws Exception;

}