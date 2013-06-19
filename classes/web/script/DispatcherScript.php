<?php
namespace classes\web\script;
use classes\ui\ModelMap;
use classes\lang\PerminatorClassLoader;
use classes\content\Context;
use conf\Resolver;
use classes\lang\StringBuilder;

/**
 * FIXME DispatcherScript에서 BeanDependencyInjector와의 분리가 필요
 * @author User
 *
 */
class DispatcherScript {
	private $context;
	private $errorHandler;

	public function doDispatch() {
		$this->initErrorHandler ();

		$context = $this->context = new Context();
		$context->classLoader = PerminatorClassLoader::getClassLoader ();

		$requestResolverClassName = Resolver::REQUEST_RESOLVER;
		$requestResolver = new $requestResolverClassName();
		$this->printPage ( $requestResolver->resolve($context), $requestResolver->findAllModelMap() );
		$this->errorHandler->publish();
	}

	private function initErrorHandler() {
		$exceptionHandler = new ExceptionHandler ();
		$this->errorHandler = new ErrorHandler ();

		if(defined('DEBUG') && DEBUG) {
			ini_set('display_errors', 1);
			set_error_handler ( array ( $this->errorHandler, "push" ), error_reporting() );
			set_exception_handler ( array ( $exceptionHandler, "publish" ) );
		}
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
class PHPError {
	public $number;
	public $msg;
	public $file;
	public $line;
	public $context;

	public function __construct($number, $msg, $file, $line, $context) {
		$this->number = $number;
		$this->msg = $msg;
		$this->file = $file;
		$this->line = $line;
		$this->context = $context;
	}

	public function __toString() {
		return "{$this->getTypeString()}: {$this->msg} in {$this->file} on line {$this->line}";
	}

	public function getTypeString() {
		switch ($this->number) {
			case E_COMPILE_ERROR: return "Compile error";
			case E_COMPILE_WARNING: return "Compile warning";
			case E_CORE_ERROR: return "Core error";
			case E_CORE_WARNING: return "Core warning";
			case E_DEPRECATED: return "Deprecated";
			case E_ERROR: return "Error";
			case E_NOTICE: return "Notice";
			case E_PARSE: return "Parse error";
			case E_RECOVERABLE_ERROR: return "Recoverable error";
			case E_STRICT: return "STRICT";
			case E_USER_DEPRECATED: return "User deprecated";
			case E_USER_ERROR: return "User error";
			case E_USER_NOTICE: return "User notice";
			case E_USER_WARNING: return "User warning";
			case E_WARNING: return "Warning";
			default: return "Unknow";
		}
	}
}
class ErrorHandler {
	private $phpErrors = array();

	public function push($number, $msg, $file, $line, $context) {
		// @FIXME Fatal Error Capture
		// header ( 'Content-Type: text/xml; charset=UTF-8', true, 500 );
		$this->phpErrors[] = new PHPError($number, $msg, $file, $line, $context);
		return true;
	}

	public function publish() {
		foreach ($this->phpErrors as $error) {
			/* @var @error PHPError */
			echo $error."\n";
		}
	}
}
class ExceptionHandler {
	const XML_WRAPPER = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<Exception type="%s">
	<Message>%s</Message>
	<File>%s</File>
	<Code>%s</Code>
	<Line>%s</Line>
	<TraceStacks>
		%s
	</TraceStacks>
</Exception>
XML;
	const TRACE = <<< XML
<TraceStack Class="%s" Function="%s" Line="%s">
	<File>%s</File>
	<Args>%s</Args>
</TraceStack>
XML;
	public function publish(\Exception $exception) {
		$strBuilder = new StringBuilder ();

		header_remove();
		header ( 'Content-Type: text/xml; charset=UTF-8', true, 500 );
		$exceptionName = get_class($exception);

		$traceExceptions = $exception->getTrace ();

		foreach ( $traceExceptions as $traceException ) {
			$argsBuilder = new StringBuilder ();

			$index = -1;
			if (array_key_exists ( $traceException, $traceException ))
				$index = count ( $traceException ['args'] );
			for($i = 0; $i < $index; $i ++) {
				$arg = $traceException ['args'] [$i];
				$ref = new \ReflectionObject ( $arg );

				if (is_object ( $arg )) {
					$argsBuilder->append ( "<Arg{$i} Class=\"{$ref->getName()}\">" );

					foreach ( $arg as $key => $value ) {
						$argsBuilder->append ( "<{$key}>" );
						$argsBuilder->append ( $value );
						$argsBuilder->append ( "</{$key}>" );
					}
					$argsBuilder->append ( "</Arg{$i}>" );
				} elseif (! is_array ( $arg )) {
					$argsBuilder->append ( "<Arg{$i}>" );
					$argsBuilder->append ( $arg );
					$argsBuilder->append ( "</Arg{$i}>" );
				}
			}

			$strBuilder->append ( sprintf ( self::TRACE, @$traceException ['class'], @$traceException ['function'], @$traceException ['line'], @$traceException ['file'], $argsBuilder->toString () ) );
		}

		printf ( self::XML_WRAPPER, $exceptionName, $exception->getMessage (), $exception->getFile (), $exception->getCode (), $exception->getLine (), $strBuilder->toString () );
	}
	private function argmentsToXml($args) {
	}
}
class BeanInitializationException extends \RuntimeException { }
class NotControllerException extends \RuntimeException { }