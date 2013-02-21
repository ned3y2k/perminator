<?php
namespace classes\web\script;

/*
 * http://static.springsource.org/spring/docs/2.5.x/api/org/springframework/web/servlet/handler/HandlerInterceptorAdapter.html
 * http://static.springsource.org/spring/docs/2.5.x/api/org/springframework/web/servlet/HandlerInterceptor.html
 * http://docs.oracle.com/javaee/1.4/api/javax/servlet/http/HttpServletRequest.html?is-external=true
 * http://docs.oracle.com/javaee/1.4/api/javax/servlet/http/HttpServletResponse.html?is-external=true
 * http://static.springsource.org/spring/docs/2.5.x/api/org/springframework/web/servlet/View.html
 */
interface HandlerInterceptor {
	function afterCompletion(HttpServletRequest $request, HttpServletResponse $response, Object $handler, Exception $ex);
	function postHandle(HttpServletRequest $request, HttpServletResponse $response, Object $handler, ModelAndView $modelAndView);
	function preHandle(HttpServletRequest $request, HttpServletResponse $response, Object $handler);
}
