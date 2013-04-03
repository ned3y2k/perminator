<?php
namespace classes\content;
use classes\web\script\http\Request;

class Context {
	private $sharedInstances;
	public $controllerInstance;
	public $invokeClassName;
	public $invokeMethodName;
	public $invokeMethodLine;
	public $classLoader;
	public $requestMethod;

	public function __construct(Context $context = null) {
		if (is_null ( $context ))
			$this->sharedInstances = array ();
		else
			$this->cloneContext ( $context );
	}
	private function cloneContext() {
		$this->sharedInstances = $context->sharedInstances;

		$this->invokeClassName = $context->invokeClassName;
		$this->invokeMethodName = $context->invokeMethodName;
	}
	public function getUri($controllerName, $methodName = null) {
		$req = Request::getInstance ( Request::GET );
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