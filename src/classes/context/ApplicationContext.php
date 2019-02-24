<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:30
 */

namespace classes\context;


use classes\handler\throwable\IUnexpectedThrowableHandler;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\io\File;
use classes\lang\ArrayUtil;
use classes\util\ServerEnvironment;

class ApplicationContext implements IApplicationContext {
	/** @var SharedUserContext 사용자 공유 Context */
	private $sharedUserContext;
	private $responseContext;
	private $requestContext;
	private $debugContext;

	private $environment;

	/** @var IUnexpectedThrowableHandler */
	private $exceptionHandler;

	public function __construct() {
		$this->responseContext   = new ResponseContext();
		$this->requestContext    = new RequestContext();
		$this->sharedUserContext = new SharedUserContext(new \DateTime());
		$this->environment       = new ServerEnvironment($this);
		$this->debugContext      = new DebugContext(_APP_ROOT_ . 'debug');
	}

	public function getSharedUserContext(): SharedUserContext { return $this->sharedUserContext; }

	public function getRequestContext(): RequestContext { return $this->requestContext; }

	public function getResponseContext(): ResponseContext { return $this->responseContext; }

	public function getServerEnvironment(): ServerEnvironment { return $this->environment; }

	public function getDebugContext(): DebugContext {return $this->debugContext; }

	/**
	 * 메서드 명 정규화
	 *
	 * @param $methodName
	 * @param $line
	 *
	 * @return string
	 */
	public function callNameNormalization($methodName, $line) {
		return str_replace('::', '-', str_replace('\\', '.', $methodName)) . "($line)";
	}

	public function getExceptionHandler(): IUnexpectedThrowableHandler { return $this->exceptionHandler; }

	/** @param IUnexpectedThrowableHandler $exceptionHandler */
	public function setExceptionHandler(IUnexpectedThrowableHandler $exceptionHandler) { $this->exceptionHandler = $exceptionHandler; }
}