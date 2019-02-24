<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:50
 */

namespace classes\test\context;


use classes\context\DebugContext;
use classes\context\IApplicationContext;
use classes\context\RequestContext;
use classes\context\ResponseContext;
use classes\context\SharedUserContext;
use classes\handler\throwable\IUnexpectedThrowableHandler;
use classes\util\ServerEnvironment;

class TestApplicationContext implements IApplicationContext {
	/** @var SharedUserContext 사용자 공유 Context */
	private $sharedUserContext;
	private $responseContext;
	private $requestContext;
	private $debugContext;

	/** @var ServerEnvironment */
	private $environment;
	/** @var bool 디버그 여부 */
	private $debug = false;
	/** @var string[] */
	private $debugFlags;

	/** @var IUnexpectedThrowableHandler */
	private $exceptionHandler;

	public function __construct() {
		if (file_exists(_APP_ROOT_ . 'debug')) {
			$this->debug      = true;
			$this->debugFlags = parse_ini_file(_APP_ROOT_ . 'debug');
		}

		$this->responseContext   = new ResponseContext();
		$this->requestContext    = new RequestContext();
		$this->sharedUserContext = new SharedUserContext(new \DateTime());
		$this->environment       = new ServerEnvironment($this);
		$this->debugContext      = new DebugContext(_APP_ROOT_ . 'debug');
	}

	/** @return SharedUserContext */
	public function getSharedUserContext(): SharedUserContext { return $this->sharedUserContext; }

	public function getRequestContext(): RequestContext { return $this->requestContext; }

	public function getResponseContext(): ResponseContext { return $this->responseContext; }

	/** @return ServerEnvironment 서버 환경 */
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