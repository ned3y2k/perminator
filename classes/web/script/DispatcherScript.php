<?php

namespace classes\web\script;

use app\controller\IndexController;
use classes\web\bind\meta\RequestParamCollection;
use classes\binder\DataBinder;
use conf\Core as CoreConfig;
use classes\Meta\Controller;
use classes\lang\ClassLoader;
use classes\support\Factory;
use classes\web\bind\meta\RequestParam;
use classes\lang\ClassNotFoundException;
use classes\web\script\http\Request;

/**
 * FIXME DispatcherScript에서 BeanDependencyInjector와의 분리가 필요
 * @author User
 *
 */
class DispatcherScript {
	/**
	 *
	 * @var ClassLoader
	 */
	private $classLoader;
	private $methodLine;
	private $className;
	public function doDispatch() {
		$this->classLoader = ClassLoader::getClassLoader ();

		$getReq = Request::getInstance ( Request::GET );

		$className = $getReq->getParameter ( "class", null );
		$className = str_replace('/', '\\', $className);
		
		if ($className == null || $className == CoreConfig::ENTRY_SCRIPT)
			$className = CoreConfig::DEFAULT_CONTROLLER;

		$className = CoreConfig::CONTROLLER_NAMESPACE_PREFIX . $className;

		$controllerName = $this->classLoader->findFullClassName ( $className );
		$controller = $this->createControllerInstance(new \ReflectionClass ( $controllerName ));
		$methodName = $getReq->getParameter ( "method", CoreConfig::CONTROLLER_DEFAULT_METHOD );

		if (! $controller instanceof Controller)
			throw new NotControllerException ( $className . " is Not Controller" );

		$controllerInstanceRef = new \ReflectionObject ( $controller );

		$methodRef = $controllerInstanceRef->getMethod ( $methodName );
		$this->className = $className;
		$this->methodLine = $methodRef->getStartLine ();

		$this->printPage ( $methodRef->invokeArgs ( $controller, $this->injectRequestParams ( $methodRef->getParameters () ) ) );
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


	private function injectRequestParams(array $refParams) {
		$params = array ();
		$classLoader = $this->classLoader;

		foreach ( $refParams as &$refParam ) {
			/* @var $refParam \ReflectionParameter */
			if (! $refParam->getClass ()->implementsInterface ( 'classes\web\bind\meta\RequestParamCollection' ))
				throw new \InvalidArgumentException ();

			$reqParamCollectionInstance = $classLoader->newInstance ( $refParam->getClass ()->name );

			$this->bindRequestParam ( $reqParamCollectionInstance );
			array_push ( $params, $reqParamCollectionInstance );
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
	private function bindRequestParam(RequestParamCollection &$reqParamCollection) {
		$requestParams = &$reqParamCollection->getRequestParams ();
		$dataBinder = new DataBinder ( $reqParamCollection->getKeyNamePrefix () );

		foreach ( $requestParams as &$reqParam ) {
			/* @var $reqParam RequestParam */
			$className = $reqParam->getClassName ();
			$desInstance = new $className ();

			$dataBinder->binding ( $desInstance, $this->getRequestData ( $reqParam->getMethod () ), $reqParam->isIncomplete () );
			if ($reqParam->isRequired () && is_null ( $desInstance ))
				throw new BeanInitializationException ( "{$this->className}[{$this->methodLine}]: {$reqParam->getClassName()} Required. but not ready" );
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
	public function printPage($page) {
		if (! is_string ( $page ) && ! ($page instanceof View) && ! is_null ( $page ))
			throw new \InvalidArgumentException ( "Return type is not View(Only accept a String or View or Null)" );

		if (is_string ( $page ) || is_null ( $page ))
			$view = new ModelAndView ( $page );
		elseif ($page instanceof View)
			$view = &$page;

		header ( "Content-Type: " . $view->getContentType () );
		echo $view->getContent ();
	}
}
class BeanInitializationException extends \RuntimeException {
}
class NotControllerException extends \RuntimeException {
}
?>