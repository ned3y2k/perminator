<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 6:43
 */

namespace classes\handler\throwable;


interface IUnexpectedThrowableHandler {
	/**
	 * @param string $exceptionClass
	 * @param string $page
	 */
	function addExceptionPage(string $exceptionClass, string $page);

	/**
	 * 예외 페이지 핸들링(저장 및 출력)
	 *
	 * @param \Throwable $throwable
	 * @throws \Exception
	 * @throws \Throwable
	 */
	function handling(\Throwable $throwable = null);
}