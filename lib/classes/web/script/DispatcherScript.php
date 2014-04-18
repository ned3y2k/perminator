<?php
namespace classes\web\script;
use classes\ui\ModelMap;
use classes\lang\PerminatorClassLoader;
use conf\Resolver;
use classes\trouble\ExceptionHandler;
use classes\trouble\ErrorHandler;

/**
 * FIXME DispatcherScript에서 BeanDependencyInjector와의 분리가 필요
 * @author User
 *
 */
class DispatcherScript {
	private $context;

	public function doDispatch(\Context $context) {
		if(is_null($context)) {
			echo "context is null";exit;
		}

		$context->classLoader = PerminatorClassLoader::getClassLoader ($context);
		$this->initErrorHandler ();

		$this->context = $context;

		$requestResolverClassName = Resolver::REQUEST_RESOLVER;
		/* @var $requestResolver \classes\resolver\request\RequestResolver */
		$requestResolver = new $requestResolverClassName();
		$this->printPage ( $requestResolver->resolve($context), $requestResolver->findAllModelMap() );
	}

	private function initErrorHandler() {
		set_exception_handler ( array ( $this, "throwException" ) );
		set_error_handler ( array ( $this, "throwError" ), error_reporting() );
	}

	public function throwError($code, $msg, $file, $line) {
		$errorHandler = new ErrorHandler ();
		$errorHandler->publish($code, $msg, $file, $line, $this->context);
	}

	public function throwException(\Exception $exception) {
		$exceptionHandler = new ExceptionHandler ();
		$exceptionHandler->publish($exception, $this->context);
	}

	public function printPage($page, array $modelMaps) {
		if (! is_string ( $page ) && ! ($page instanceof View) && ! is_null ( $page )) {
			throw new \InvalidArgumentException ( "Return type is not View(Only accept a String or View or Null)" );
		}

		// FIXME ViewResolver Start
		if (is_string ( $page ) || is_null ( $page )) {
			$this->resolveRedirect($page);
			$view = new ModelAndView ( $page );
		} elseif ($page instanceof View) {
			$view = &$page;
		}

		if($view instanceof ModelAndView && count($modelMaps) > 0) {
			$modelMap = new ModelMap();

			foreach ($modelMaps as $curModelMap) {
				$modelMap->merge($curModelMap);
			}

			$view->setModelMap($modelMap);
		}
		// FIXME ViewResolver End

		header ( "Content-Type: " . $view->getContentType () );
		echo $view->getContent ();
	}

	private function resolveRedirect($page) {
		if(is_string($page) && substr_count($page, 'redirect:') > 0) {
			header("Location:".substr($page, 9), true);
			exit;
		}
	}
}