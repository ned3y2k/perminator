<?php
namespace classes\web\script;

interface HandlerInterceptor {
	/**
	 * http://static.springsource.org/spring/docs/3.0.x/javadoc-api/org/springframework/web/servlet/HandlerInterceptor.html
	 */

	function afterCompletion(HttpServletRequest $request, HttpServletResponse $response, Object $handler, Exception $ex);
	function postHandle(HttpServletRequest $request, HttpServletResponse $response, Object $handler, ModelAndView $modelAndView);

	/**
	 * @param HttpServletRequest $request
	 * @param HttpServletResponse $response
	 * @param Object $handler
	 * @return boolean
	 */
	function preHandle(HttpServletRequest $request, HttpServletResponse $response, Object $handler);
}
