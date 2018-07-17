<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 4:50
 */

namespace classes\api;


use classes\context\IApplicationContext;
use classes\api\model\JSONResult;

/**
 * Interface API
 *
 * @package api
 */
abstract class APIProcessor {
	/** @var IApplicationContext */
	protected $applicationContext;
	/** @var string */
	private $invokedMethod;
	/** @var int */
	private $invokedApiVersion;

	/**
	 * @param IApplicationContext $applicationContext
	 * @return void
	 */
	public final function setApplicationContext(IApplicationContext $applicationContext) { $this->applicationContext = $applicationContext; }

	/** @return string 설명 */
	public function getDescription() { return ''; }

	/** @param string $msg 출력 메시지 */
	protected final function checkSecureLayerConnection($msg = 'https 에서만 접근 가능합니다.') {
		if(request_scheme() !== 'https' && !$this->applicationContext->getServerEnvironment()->isForceHttp()) {
			throw new \RuntimeException($msg);
		}
	}

	protected $jsonRequestData;
	protected final function getJsonRequestData() {
		if($this->jsonRequestData)
			return $this->jsonRequestData;

		$requestContext = $this->applicationContext->getRequestContext();

		if(!strtolower($requestContext->compareContentType('application/json')))
			throw new \RuntimeException('Request Conent type is not application/json');

		$this->jsonRequestData = json_decode($requestContext->rawData());
		return $this->jsonRequestData;
	}

	/** @return IApplicationContext */
	protected final function getApplicationContext() {
		if(!$this->applicationContext)
			throw new \RuntimeException("Application Context is null");
		return $this->applicationContext;
	}

	/** @return JSONResult|mixed */
	public abstract function doPerform();

	/** @return string */
	protected final function getInvokedMethod() { return $this->invokedMethod; }

	/** @param string $invokedMethod */
	public final function setInvokedMethod($invokedMethod) { $this->invokedMethod = $invokedMethod; }

	/** @return int */
	protected final function getInvokedApiVersion() { return $this->invokedApiVersion; }

	/** @param int $invokedApiVersion */
	public final function setInvokedApiVersion($invokedApiVersion) { $this->invokedApiVersion = $invokedApiVersion; }
}