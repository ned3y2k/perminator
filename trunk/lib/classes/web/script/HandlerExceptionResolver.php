<?php
namespace classes\web\script;

interface HandlerExceptionResolver {
	/** http://static.springsource.org/spring/docs/3.0.x/javadoc-api/org/springframework/web/servlet/HandlerExceptionResolver.html  */
	function resolveException(HttpServletRequest $request, HttpServletResponse $response, Object $handler, Exception $ex);
}