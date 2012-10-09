<?php
namespace classes\web\script;

use classes\web\bind\meta\RequestParamCollection;
use classes\binder\DataBinder;
use conf\Core as CoreConfig;
use classes\Meta\Controller;
use classes\lang\ClassLoader;
use classes\support\Factory;
use classes\web\bind\meta\RequestParam;
use classes\lang\ClassNotFoundException;
use classes\web\script\http\Request;

class DispatcherScript {
	/**
	 * @var DataBinder
	 */
	public $dataBinder;

	/**
	 * @var ClassLoader
	 */
	private $classLoader;

	private $methodLine;
	private $className;

	public function doDispatch() {

		$this->dataBinder = new DataBinder();
		$this->classLoader = ClassLoader::getClassLoader();

		$getReq = Request::getInstance(Request::GET);

		$className = $getReq->getParameter("class", null);
		if ($className == null || $className == CoreConfig::ENTRY_SCRIPT)
			$className = CoreConfig::DEFAULT_CONTROLLER;

		$className = CoreConfig::CONTROLLER_NAMESPACE_PREFIX . $className;

		$controller = $this->classLoader->newInstance($className);
		$methodName = $getReq->getParameter("method", CoreConfig::CONTROLLER_DEFAULT_METHOD);

		if (!$controller instanceof Controller)
			throw new NotControllerException($className . " is Not Controller");

		$controllerRef = new \ReflectionObject($controller);
		$this->injectPropertiesValue($controllerRef);

		$methodRef = $controllerRef->getMethod($methodName);
		$this->className = $className;
		$this->methodLine = $methodRef->getStartLine();

		$this->printPage($methodRef->invokeArgs($controller, $this->injectMethodParams($methodRef->getParameters())));
	}

	/**
	 * @param controllerRef \ReflectionObject
	 */
	private function injectPropertiesValue(\ReflectionObject &$controllerRef) {
		$propsRef = $controllerRef->getProperties();
		foreach ($propsRef as &$propRef) {
			/* @var $propRef \ReflectionProperty */
			// 			$propRef->getDocComment()

			$lockRequierd = $this->propertieUnLock($propRef);

			if ($lockRequierd)
				$this->propertieLock($propRef);
		}
	}

	private function injectMethodParams(array $refParams) {
		$params = array();
		$classLoader = $this->classLoader;

		foreach ($refParams as &$refParam) {
			/* @var $refParam \ReflectionParameter */
			if (!$refParam->getClass()->implementsInterface('classes\web\bind\meta\RequestParamCollection'))
				throw new \InvalidArgumentException();

			$reqParamCollectionInstance = $classLoader->newInstance($refParam->getClass()->name);

			$this->bindParam($reqParamCollectionInstance);
			array_push($params, $reqParamCollectionInstance);
		}

		return $params;
	}

	private function propertieUnLock(\ReflectionProperty &$propRef) {
		if (!$propRef->isPublic()) {
			$propRef->setAccessible(true);
			return true;
		}
		return false;
	}

	private function bindParam(RequestParamCollection &$reqParamCollection) {
		$requestParams = &$reqParamCollection->getRequestParams();
		$dataBinder = &$this->dataBinder;

		foreach ($requestParams as &$reqParam) {
			/* @var $reqParam RequestParam */
			$data = null;
			switch ($reqParam->getMethod()) {
			case RequestParam::METHOD_COOKIE:
				$data = Request::getInstance(Request::COOKIE);
				break;
			case RequestParam::METHOD_SESSION:
				$data = Request::getInstance(Request::SESSION);
				break;
			case RequestParam::METHOD_POST:
				$data = Request::getInstance(Request::POST);
				break;
			case RequestParam::METHOD_GET:
				$data = Request::getInstance(Request::GET);
				break;
			}

			$className = $reqParam->getClassName();
			$desInstance = new $className();

			$dataBinder->binding($desInstance, $data);
			if ($reqParam->getRequired() && is_null($desInstance))
				throw new BeanInitializationException(
						"{$this->className}[{$this->methodLine}]: {$reqParam->getClassName()} Required. but not ready");
			$reqParam->value = $desInstance;
		}
	}

	private function propertieLock(\ReflectionProperty &$propRef) {
		if (!$propRef->isPublic()) {
			!$propRef->setAccessible(false);
		}
	}

	public function printPage($page) {
		if(!is_string($page) && !($page instanceof View) && !is_null($page))
			throw new \InvalidArgumentException("Return type is not View(Only accept a String or View or Null)");

		if (is_string($page) || is_null($page))
			$view = new ModelAndView($page);
		elseif($page instanceof View)
			$view = &$page;

		/* @var $view ModelAndView */
		header("Content-Type: ".$view->getContentType());
		echo $view->getContent();
	}
}
class BeanInitializationException extends \RuntimeException {
}
class NotControllerException extends \RuntimeException {
}
?>