<?php
namespace classes\web\script;
use classes\ui\ModelMap;
use app\controller\IndexController;
use classes\web\bind\meta\RequestParamCollection;
use classes\binder\DataBinder;
use conf\Core as CoreConfig;
use classes\meta\Controller;
use classes\lang\ClassLoader;
use classes\support\Factory;
use classes\web\bind\meta\RequestParam;
use classes\lang\ClassNotFoundException;
use classes\web\script\http\Request;

require_once 'conf/MappingExceptionResolver.php';

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
	private $modelMap = array();

	public function doDispatch() {
		$this->classLoader = ClassLoader::getClassLoader ();

		$getReq = Request::getInstance ( Request::GET );

		$className = $getReq->getParameter ( "class", null );
		$className = str_replace('/', '\\', $className);

		if ($className == null || $className == CoreConfig::ENTRY_SCRIPT) {
			$className = CoreConfig::DEFAULT_CONTROLLER;
		}

		$className = CoreConfig::CONTROLLER_NAMESPACE_PREFIX . $className;

		$controllerName = $this->classLoader->findFullClassName ( $className );
		$controller = $this->createControllerInstance(new \ReflectionClass ( $controllerName ));
		$methodName = $getReq->getParameter ( "method", CoreConfig::CONTROLLER_DEFAULT_METHOD );

		if (! $controller instanceof Controller)
			throw new NotControllerException ( $className . " is Not Controller" );

		$controllerInstanceRef = new \ReflectionObject ( $controller );

		/* @var $methodRef \ReflectionMethod */
		$methodRef = $controllerInstanceRef->getMethod ( $methodName );
		$this->className = $className;
		$this->methodLine = $methodRef->getStartLine ();

		try {
			$controllerResult = $methodRef->invokeArgs ( $controller, $this->injectParams ( $methodRef->getParameters () ) );
		} catch (\Exception $ex) {
			global $MappingExceptionResolver;
			var_dump($MappingExceptionResolver[get_class($ex)]);
			exit;
			throw $ex;
		}
		$this->printPage ( $controllerResult );
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


	private function injectParams(array $refParams) {
		$params = array ();
		$classLoader = $this->classLoader;

		foreach ( $refParams as &$refParam ) {
			/* @var $refParam \ReflectionParameter */

			$type = $refParam->getClass ();
			if($refParam->isArray()) {
				$paramInstance = Request::getInstance(Request::GET)->getParameters();
			} elseif (is_null($type)) {
				$defaultValue = $refParam->isDefaultValueAvailable() ? $refParam->getDefaultValue() : null;
				$paramInstance = Request::getInstance(Request::GET)->getParameter($refParam->getName(), $defaultValue);
			} elseif ($type->implementsInterface ( 'classes\web\bind\meta\RequestParamCollection' )) {
				$paramInstance = $classLoader->newInstance ( $refParam->getClass ()->name );
				$this->bindRequestParam ( $paramInstance );
			} elseif($type->name == 'classes\ui\ModelMap') {
				$paramInstance = new ModelMap();
				array_push($this->modelMap, $paramInstance);
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
	private function bindRequestParam(RequestParamCollection &$reqParamCollection) {
		$requestParams = &$reqParamCollection->getRequestParams ();
		$dataBinder = new DataBinder ( $reqParamCollection->getKeyNamePrefix () );

		foreach ( $requestParams as &$reqParam ) {
			/* @var $reqParam RequestParam */
			$className = $reqParam->getClassName ();
			$desInstance = new $className ();

			$dataBinder->binding ( $desInstance, $this->getRequestData ( $reqParam->getMethod () ), $reqParam->isIncomplete () );
			if ($reqParam->isRequired () && is_null ( $desInstance )) { throw new BeanInitializationException ( "{$this->className}[{$this->methodLine}]: {$reqParam->getClassName()} Required. but not ready" ); }
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
		if (! is_string ( $page ) && ! ($page instanceof View) && ! is_null ( $page )) {
			throw new \InvalidArgumentException ( "Return type is not View(Only accept a String or View or Null)" );
		}

		if (is_string ( $page ) || is_null ( $page )) {
			$this->processRedirect($page);
			$view = new ModelAndView ( $page );
		} elseif ($page instanceof View) {
			$view = &$page;
		}

		if($view instanceof ModelAndView && count($this->modelMap) > 0) {
			$modelMap = new ModelMap();

			$index = count($this->modelMap);
			for ($i = 0; $i < $index; $i++) {
				$modelMap->merge($this->modelMap[$i]);
			}

			$view->setModelMap($modelMap);
		}

		header ( "Content-Type: " . $view->getContentType () );
		echo $view->getContent ();
	}
	private function processRedirect($page) {
		if(is_string($page) && substr_count($page, 'redirect:') > 0) {
			header("Location:".substr($page, 9), true);
			exit;
		}
	}
}
class BeanInitializationException extends \RuntimeException {
}
class NotControllerException extends \RuntimeException {
}