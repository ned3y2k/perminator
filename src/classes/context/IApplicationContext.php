<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:33
 */

namespace classes\context;


use classes\handler\throwable\IThrowableHandler;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\util\ServerEnvironment;

interface IApplicationContext {

	/** @return bool */
	public function isDebug();

	public function getSharedUserContext(): SharedUserContext;
	public function getRequestContext(): RequestContext;
	public function getResponseContext(): ResponseContext;
	public function getServerEnvironment(): ServerEnvironment;

	/**
	 * 디버그 정보 입력
	 *
	 * @param string       $fileName 파일경로
	 * @param object|mixed $object   기록할 내용
	 *
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function writeDebugInfo($fileName, $object);

	/**
	 * 메서드 명 정규화
	 *
	 * @param $methodName
	 * @param $line
	 *
	 * @return string
	 */
	public function callNameNormalization($methodName, $line);

	public function getExceptionHandler(): IThrowableHandler;

	/** @param IThrowableHandler $exceptionHandler */
	public function setExceptionHandler(IThrowableHandler $exceptionHandler);

	/**
	 * @param string|null $key
	 * @param string      $default 값이 지정되어 있지 않을때 기본 값
	 *
	 * @return array|string
	 */
	public function getDebugFlag($key = null, $default = null);
}