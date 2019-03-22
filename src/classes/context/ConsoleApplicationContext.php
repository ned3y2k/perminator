<?php
/**
 * User: Kyeongdae
 * Date: 2019-03-22
 * Time: 오후 2:56
 */

namespace classes\context;


use classes\handler\throwable\IUnexpectedThrowableHandler;
use classes\util\ServerEnvironment;

class ConsoleApplicationContext implements IApplicationContext {
	private $responseContext;
	private $requestContext;
	private $sharedUserContext;
	private $environment;
	private $debugContext;
	private $exceptionHandler;

	public function __construct() {
		$this->responseContext   = new ResponseContext();
		$this->requestContext    = new RequestContext();
		$this->sharedUserContext = new SharedUserContext(new \DateTime());
		$this->environment       = new ServerEnvironment($this);
		$this->debugContext      = new DebugContext(_APP_ROOT_ . 'debug');
	}

	public function getSharedUserContext(): SharedUserContext {
		return $this->sharedUserContext;
	}

	public function getRequestContext(): RequestContext {
		return $this->requestContext;
	}

	public function getResponseContext(): ResponseContext {
		$this->responseContext;
	}

	public function getServerEnvironment(): ServerEnvironment {
		return $this->environment;
	}

	public function getDebugContext(): DebugContext {
		return $this->debugContext;
	}

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