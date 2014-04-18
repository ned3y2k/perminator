<?php
namespace classes\resolver\request;

use classes\web\script\http\Request;
use conf\Core;
use classes\stereotype\Controller;
use classes\web\bind\meta\RequestParam;
use classes\binder\DataBinder;
use classes\ui\ModelMap;
use classes\trouble\exception\core\NotControllerException;
use classes\trouble\exception\core\BeanInitializationException;

class ClassNameAndMethodNameResolver implements RequestResolver {
	private $context;
	private $modelMaps = array();

	public function resolve(\Context $context) {
		$this->context = $context;

		try {
			$this->context->controllerInstance = $this->resolveControllerInstance();
			return $this->resolveMethod();
		} catch (\Exception $ex) {
			throw $ex;
		}
	}

	private function resolveControllerInstance() {
		$context = $this->context;
		$context->invokeClassName = str_replace ( '/', '\\', Request::getInstance ( Request::GET )->getParameter ( 'class', null ) );

		if ($context->invokeClassName == null || $context->invokeClassName == Core::ENTRY_SCRIPT) {
			$context->invokeClassName = Core::DEFAULT_CONTROLLER;
		}

		$context->invokeClassName = Core::CONTROLLER_NAMESPACE_PREFIX . $context->invokeClassName;
		$context->invokeClassName = $context->classLoader->findFullClassName ( $context->invokeClassName );
		$controllerInstance = $this->createControllerInstance ( new \ReflectionClass ( $context->invokeClassName ) );

		if (! $controllerInstance instanceof Controller)
			throw new NotControllerException ( $context->invokeClassName . " is Not Controller" );

		$controllerInstance->setContext($context);
		return $controllerInstance;
	}

	public function findAllModelMap() {
		return $this->modelMaps;
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

	private function getConstructorArgsInstances(\ReflectionMethod $constructorRef) {
		$paramRefs = $constructorRef->getParameters ();

		$constructorArgs = array ();
		foreach ( $paramRefs as $paramRef ) {
			/* @var $paramRef \ReflectionParameter */
			array_push ( $constructorArgs, $paramRef->getClass ()->newInstance () );
		}

		return $constructorArgs;
	}

	private function resolveMethod() {
		$context = $this->context;
		$context->invokeMethodName = Request::getInstance ( Request::GET )->getParameter ( 'method', Core::CONTROLLER_DEFAULT_METHOD );

		$controllerInstanceRef = new \ReflectionObject ( $this->context->controllerInstance );

		/* @var $methodRef \ReflectionMethod */
		$methodRef = $controllerInstanceRef->getMethod ( $context->invokeMethodName ); // @TODO 메소드 없을때 \ReflectionException 내버림
		$this->context->invokeMethodLine = $methodRef->getStartLine ();

		try {
			$controllerResult = $methodRef->invokeArgs ( $context->controllerInstance, $this->lookupParams ( $methodRef->getParameters () ) );
		} catch (\Exception $ex) {
			throw $ex;
		}

		return $controllerResult;
	}

	private function lookupParams(array $refParams) {
		$params = array ();
		$classLoader = $this->context->classLoader;

		foreach ( $refParams as &$refParam ) {
			/* @var $refParam \ReflectionParameter */

			$type = $refParam->getClass ();
			if($refParam->isArray()) {
				$paramInstance = Request::getInstance(Request::GET)->getParameters();
			} elseif (is_null($type)) {
				$defaultValue = $refParam->isDefaultValueAvailable() ? $refParam->getDefaultValue() : null;
				$paramInstance = Request::getInstance(Request::GET)->getParameter($refParam->getName(), $defaultValue);
			} elseif ($type->implementsInterface ( '\classes\web\bind\meta\RequestParamCollection' )) {
				$paramInstance = $classLoader->newInstance ( $refParam->getClass ()->name );
				$this->bindRequestParam ( $paramInstance );
			} elseif($type->name == 'classes\ui\ModelMap') {
				$paramInstance = new ModelMap();
				array_push($this->modelMaps, $paramInstance);
			} else {
				$dataBinder = new DataBinder();
				$paramInstance = $classLoader->newInstance ( $refParam->getClass ()->name );
				$dataBinder->binding($paramInstance, Request::getInstance(Request::POST)->getParameters());
			}

			array_push ( $params, $paramInstance );
		}

		return $params;
	}
	private function propertieUnLock(\ReflectionProperty &$propRef) {
		if (! $propRef->isPublic ()) {
			$propRef->setAccessible ( true );
			return true;
		}
		return false;
	}
	private function bindRequestParam(\classes\web\bind\meta\RequestParamCollection &$reqParamCollection) {
		$requestParams = &$reqParamCollection->getRequestParams ();
		$dataBinder = new DataBinder ( $reqParamCollection->getKeyNamePrefix () );

		foreach ( $requestParams as &$reqParam ) {
			/* @var $reqParam RequestParam */
			$className = $reqParam->getClassName ();
			$desInstance = new $className ();

			$dataBinder->binding ( $desInstance, $this->getRequestData ( $reqParam->getMethod () ), $reqParam->isIncomplete () );
			if ($reqParam->isRequired () && is_null ( $desInstance )) { throw new BeanInitializationException( "{$this->context->invokeClassName}[{$this->context->invokeMethodLine}]: {$reqParam->getClassName()} Required. but not ready" ); }
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