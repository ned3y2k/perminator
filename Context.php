<?php
namespace classes\context;

use classes\web\script\http\Request;
class Context {
	private $sharedInstances;
	public $controllerInstance;
	public $invokeClassName;
	public $invokeMethodName;
	public $invokeMethodLine;
	public $classLoader;
	public $requestMethod;
	private $includePaths = array();

	/**
	 * @return \classes\content\Context
	*/
	public static function getSharedContext() {
		static $context = null;
		return is_null($context) ? $context = new self() : $context;
	}

	public function __construct(Context $context = null, \Closure $sessionControlFunc = null) {
		if(!is_null($sessionControlFunc)) $sessionControlFunc();
		else {
			session_start();
		}

		if (is_null ( $context ))
			$this->sharedInstances = array ();
		else
			$this->cloneContext ( $context );
	}

	/**
	 * @param string $includePath
	 */
	public function addIncludePath($includePath) {
		if(is_null($includePath) || !is_string($includePath)) throw new \InvalidArgumentException("invalid include path");

		// FIXME include path가 있는곳인지 없는 곳인지 검증하는 코드 추가
		$this->includePaths[] = $includePath;
	}

	public function getIncludePaths() {
		return $this->includePaths;
	}

	public function __destruct() {
		session_write_close();
	}

	private function cloneContext() {
		$this->sharedInstances = $context->sharedInstances;

		$this->invokeClassName = $context->invokeClassName;
		$this->invokeMethodName = $context->invokeMethodName;
	}
	public function getUri($controllerName, $methodName = null) {
		$req = Request::getInstance ( Request::GET ); // TODO 요 부분 수정 필요
		$class = $req->getParameter ( 'class' );
		$method = is_null ( $methodName ) ? $req->getParameter ( 'method' ) : $methodName;

		return is_null ( $method ) ? $class : $class . "." . $method;
	}
	public function addSharedInstance($name, $instance) {
		$this->sharedInstances [$name] = $instance;
	}
	public function getSharedInstance($name) {
		return $this->getSharedInstance ( $name );
	}
	public function getInvokeClassName() {
		return $this->invokeClassName;
	}
	public function getInvokeMethodName() {
		return $this->invokeMethodName;
	}
}