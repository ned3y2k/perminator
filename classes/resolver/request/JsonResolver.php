<?php
namespace classes\resolver\request;
use classes\resolver\request\RequestResolver;
use classes\inflator\config\json\RequestMapInflator;
use conf\Core;
use classes\web\script\http\Request;
use classes\stereotype\Controller;
use classes\web\script\NotControllerException;
use classes\web\bind\meta\RequestParam;
use classes\binder\DataBinder;
use classes\web\script\BeanInitializationException;
use classes\ui\ModelMap;

class JsonResolver implements RequestResolver {
	private $context;
	private $uri;
	private $requestMap;

	private function findRequestMapping($requestMethod, $name) {
		static $config = null;
		$config = is_null($config) ? RequestMapInflator::inflate() : $config;

		$requestMethod = strtoupper($requestMethod);
		return $config->$requestMethod->$name;
	}


	public function resolve(\classes\content\Context $context) {
		$this->context = $context;

		$defaultUri = "index";
		$context->requestMethod = $_SERVER["REQUEST_METHOD"];
		$uri = $this->uri = Request::getInstance(Request::GET)->getParameter('uri', $defaultUri);
		$this->requestMap = $this->findRequestMapping($context->requestMethod, $uri == "" ? $defaultUri : $uri);

		$this->fillContextFullClassName();
		$this->fillContextMethodName();

		try {
			$this->context->controllerInstance = $this->resolveControllerInstance();
			return $this->resolveMethod();
		} catch (\Exception $ex) {
			throw $ex;
		}
	}

	private function fillContextFullClassName() {
		$context = $this->context;
		$context->invokeClassName = str_replace ( '/', '\\', $this->requestMap->className );

		if ($context->invokeClassName == null || $context->invokeClassName == Core::ENTRY_SCRIPT) {
			$context->invokeClassName = Core::DEFAULT_CONTROLLER;
		}

		$context->invokeClassName = Core::CONTROLLER_NAMESPACE_PREFIX . $context->invokeClassName;
		$context->invokeClassName = $context->classLoader->findFullClassName ( $context->invokeClassName );
	}

	private function fillContextMethodName() {
		$this->context->invokeMethodName = $this->requestMap->methodName;
	}

	private function resolveControllerInstance() {
		$controllerInstance = $this->createControllerInstance ( new \ReflectionClass ( $this->context->invokeClassName ) );

		if (! $controllerInstance instanceof Controller)
			throw new NotControllerException ( $this->context->invokeClassName . " is Not Controller" );

		$controllerInstance->setContext($this->context);
		return $controllerInstance;
	}

	private function createControllerInstance($controllerClassRef) {
		$controllerConstructor = $controllerClassRef->getConstructor ();
		if(!is_null($controllerConstructor)) {
			$constructorArgsInstances = $this->getConstructorArgsInstances ( $controllerConstructor );
			return $controllerClassRef->newInstanceArgs ( $constructorArgsInstances );
		} else {
			return $controllerClassRef->newInstance();
		}
	}

	private function resolveMethod() {
		$context = $this->context;
		$controllerInstanceRef = new \ReflectionObject ( $this->context->controllerInstance );

		/* @var $methodRef \ReflectionMethod */
		$methodRef = $controllerInstanceRef->getMethod ( $context->invokeMethodName ); // @TODO 메소드 없을때 \ReflectionException 내버림
		$this->context->invokeMethodLine = $methodRef->getStartLine ();

		try {
			$controllerResult = $methodRef->invokeArgs ( $context->controllerInstance, $this->injectParams ( $methodRef->getParameters () ) );
		} catch (\Exception $ex) {
			throw $ex;
		}

		return $controllerResult;
	}
	private function injectParams(array $refParams) {
		$params = array ();
		$classLoader = $this->context->classLoader;

		foreach ( $refParams as &$refParam ) {
			/* @var $reqParam \ReflectionParameter */

			$type = $refParam->getClass ();
			$paramName = $refParam->getName();
			$reqParam = $this->requestMap->params->$paramName;
			$method = $this->findMethod($reqParam);

			$defaultValue = $this->findDefaultValue($reqParam, $refParam);
			$reqParamName = $this->findReqParamName($paramName, $reqParam);

 			if($refParam->isArray()) {
				$paramInstance = Request::getInstance($method)->getParameters();
			} elseif (is_null($type)) {
				$paramInstance = Request::getInstance($method)->getParameter($reqParamName, $defaultValue);
			} elseif ($type->implementsInterface ( '\classes\web\bind\meta\RequestParamCollection' )) {
				$paramInstance = $classLoader->newInstance ( $refParam->getClass ()->name );
				$this->bindRequestParam ( $paramInstance );
			} elseif($type->name == 'classes\ui\ModelMap') {
				$paramInstance = new ModelMap();
				array_push($this->modelMap, $paramInstance);
			} else {
				$dataBinder = new DataBinder();
				$paramInstance = $classLoader->newInstance ( $refParam->getClass ()->name );
				$dataBinder->binding($paramInstance, Request::getInstance($method)->getParameters());
			}

			if($reqParam->require && $paramInstance == null) throw new BeanInitializationException ( "{$this->context->invokeClassName}[{$this->context->invokeMethodLine}]: {$reqParam->getClassName()} Required. but not ready" );
			array_push ( $params, $paramInstance );
		}

		return $params;
	}
	private function findMethod($reqParam) {
		$reqParamArray = get_object_vars($reqParam);
		$methodString = array_key_exists("method", $reqParamArray) ? $reqParamArray['method'] : $this->context->requestMethod;
		return $this->makeMethodTypeFromString($methodString);
	}
	private function makeMethodTypeFromString($string) {
		switch (strtoupper($string)) {
			case "GET":
				return Request::GET;
			case "POST":
				return Request::POST;
			case "SESSION":
				return Request::SESSION;
			case "COOKIE":
				return Request::COOKIE;
			default:
				throw new \Exception("unknown param method type");
		}
	}
	private function findDefaultValue($reqParam, $refParam) {
		$reqParamArray = get_object_vars($reqParam);
		$defaultValue = array_key_exists('default', $reqParamArray) ? $reqParamArray['default'] : null;
		$defaultValue = $refParam->isDefaultValueAvailable() ? $refParam->getDefaultValue() : $defaultValue;

		return $defaultValue;
	}
	private function findReqParamName($paramName, $reqParam) {
		$reqParamArray = get_object_vars($reqParam);
		return array_key_exists("value", $reqParamArray) ? $reqParamArray['value'] : $paramName;
	}
	private function bindRequestParam(\classes\web\bind\meta\RequestParamCollection &$reqParamCollection) {
		$requestParams = &$reqParamCollection->getRequestParams ();
		$dataBinder = new DataBinder ( $reqParamCollection->getKeyNamePrefix () );

		foreach ( $requestParams as &$reqParam ) {
			/* @var $reqParam RequestParam */
			$className = $reqParam->getClassName ();
			$desInstance = new $className ();

			$dataBinder->binding ( $desInstance, $this->getRequestData ( $reqParam->getMethod () ), $reqParam->isIncomplete () );
			if ($reqParam->isRequired () && is_null ( $desInstance )) { throw new BeanInitializationException ( "{$this->context->invokeClassName}[{$this->context->invokeMethodLine}]: {$reqParam->getClassName()} Required. but not ready" ); }
			$reqParam->value = $desInstance;
		}
	}
	private function getRequestData(&$reqMethodType) {
		$data = null;
		switch ($reqMethodType) {
			case RequestParam::METHOD_COOKIE :
				$data = Request::getInstance ( Request::COOKIE );
				break;
			case RequestParam::METHOD_SESSION :
				$data = Request::getInstance ( Request::SESSION );
				break;
			case RequestParam::METHOD_POST :
				$data = Request::getInstance ( Request::POST );
				break;
			case RequestParam::METHOD_GET :
				$data = Request::getInstance ( Request::GET );
				break;
		}
		return $data;
	}
	private function propertieLock(\ReflectionProperty &$propRef) {
		if (! $propRef->isPublic ()) {
			! $propRef->setAccessible ( false );
		}
	}
}