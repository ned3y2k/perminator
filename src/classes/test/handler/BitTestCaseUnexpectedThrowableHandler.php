<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:00
 */

namespace classes\test\handler;

use classes\handler\throwable\IUnexpectedThrowableHandler;

class BitTestCaseUnexpectedThrowableHandler implements IUnexpectedThrowableHandler {
	/**
	 * @param string $exceptionClass 예외 클래스명
	 * @param string $page           페이지 파일 경로
	 */
	public function addExceptionPage(string $exceptionClass, string $page) { }

	/**
	 * @param \Throwable|null $throwable
	 * @throws \Throwable
	 */
	function handling(\Throwable $throwable = null) {
		throw $throwable;
	}
}