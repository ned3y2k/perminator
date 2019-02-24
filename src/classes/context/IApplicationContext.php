<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:33
 */

namespace classes\context;


use classes\handler\throwable\IUnexpectedThrowableHandler;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\util\ServerEnvironment;

interface IApplicationContext {
	public function getSharedUserContext(): SharedUserContext;
	public function getRequestContext(): RequestContext;
	public function getResponseContext(): ResponseContext;
	public function getServerEnvironment(): ServerEnvironment;
	public function getDebugContext(): DebugContext;

	/**
	 * 메서드 명 정규화
	 *
	 * @param $methodName
	 * @param $line
	 *
	 * @return string
	 */
	public function callNameNormalization($methodName, $line);

	public function getExceptionHandler(): IUnexpectedThrowableHandler;

	/** @param IUnexpectedThrowableHandler $exceptionHandler */
	public function setExceptionHandler(IUnexpectedThrowableHandler $exceptionHandler);
}