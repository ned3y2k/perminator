<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오후 1:11
 */

namespace classes\web\dispatch\interceptor;


use classes\web\IHandlerInterceptor;
use classes\web\IInterceptorFinder;

class InterceptorManager {
	/** @var IInterceptorFinder[] */
	private $interceptorFinders = array();

	/** @return IHandlerInterceptor[] */
	public function findInterceptors() {
		$foundInterceptors = array();

		foreach ($this->interceptorFinders as $interceptorFinder) {
			if ($_SERVER['REQUEST_URI'] != $_SERVER['URL']) { // rewrite를 사용할때, url parameter 가 있을때
				$newFoundInterceptors = array_merge($foundInterceptors, $interceptorFinder->findInterceptors($_SERVER['REQUEST_URI']));
				$newFoundInterceptors = array_merge($newFoundInterceptors, $interceptorFinder->findInterceptors($_SERVER['URL']));

				$foundInterceptors = array_merge($foundInterceptors, $newFoundInterceptors); // FIXME 중복 되는 인터셉터 제거 필요
			} else {
				$foundInterceptors = array_merge($foundInterceptors, $interceptorFinder->findInterceptors($_SERVER['REQUEST_URI']));
			}
		}

		return $foundInterceptors;
	}

	/**
	 * @param IInterceptorFinder $interceptorFinder
	 *
	 * @return void
	 */
	public function addInterceptorFinder(IInterceptorFinder $interceptorFinder) { $this->interceptorFinders[] = $interceptorFinder; }
}