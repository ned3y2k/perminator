<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 2014-12-23
 * 시간: 오후 6:20
 */

namespace classes\web;


interface IInterceptorFinder {
	/**
	 * @param string $url
	 * @return IHandlerInterceptor[]
	 */
	public function findInterceptors($url);
}