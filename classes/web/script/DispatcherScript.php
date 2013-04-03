<?php
namespace classes\web\script;
use classes\ui\ModelMap;
use classes\lang\ClassLoader;
use classes\content\Context;
use conf\Resolver;

/**
 * FIXME DispatcherScript에서 BeanDependencyInjector와의 분리가 필요
 * @author User
 *
 */
class DispatcherScript {
	private $context;
	private $modelMap = array();

	public function doDispatch() {
		$context = $this->context = new Context();
		$context->classLoader = ClassLoader::getClassLoader ();

		$requestResolverClassName = Resolver::REQUEST_RESOLVER;
		$requestResolver = new $requestResolverClassName();
		$this->printPage ( $requestResolver->resolve($context) );
	}


	public function printPage($page) {
		if (! is_string ( $page ) && ! ($page instanceof View) && ! is_null ( $page )) {
			throw new \InvalidArgumentException ( "Return type is not View(Only accept a String or View or Null)" );
		}

		// FIXME ViewResolver Start
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
		// FIXME ViewResolver End

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
class BeanInitializationException extends \RuntimeException { }
class NotControllerException extends \RuntimeException { }