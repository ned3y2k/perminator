<?php
/**
 * User: Kyeongdae
 * Date: 2015-02-26
 * Time: 오전 12:13
 */

namespace classes\web\dispatch\resolver;
use classes\web\IInterceptorFinder;


/**
 * Interface IDispatchResolver
 * 웹 요청을 해결(반응)
 * @package classes\web\dispatch
 */
interface IDispatcherResolver {
	/**
	 * @param string $className
	 * @throws \Exception
	 * @return void|mixed
	 */
	public function resolve($className);

	/**
	 * @param IInterceptorFinder $interceptorFinder
	 * @return void
	 */
	public function addInterceptorFinder(IInterceptorFinder $interceptorFinder);
}