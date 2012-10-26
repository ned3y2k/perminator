<?php

namespace classes\lang;

use classes\component\factory\AbstractFactory;
use classes\support\ClassAliasRepository;

if (! defined ( 'NIL' ))
	define ( 'NIL', null );

	// require_once $_SERVER ['DOCUMENT_ROOT'] . '/perminator.inc.php';
require_once 'perminator.inc.php';
// require_once 'LoadedClassMeta.php';

/**
 * FIXME ClassAlias 관련 전부 AbstactFactory로 이동 하여야 할것
 *
 * @author User
 *         Perminator Class Loader
 */
class ClassLoader {
	private static $instance = NIL;
	private $includePaths;
	private $includedClasses;
	private $classAliasRepository;
	private $objectFactory;
	public function __construct() {
		global $incPath;
		$this->exceptionHandler = new ExceptionHandler ();
		$this->errorHandler = new ErrorHandler ();

		spl_autoload_register ( array (
				$this,
				"includeByClassName"
		) );
		$this->includePaths = explode ( PATH_SEPARATOR, get_include_path () );
		$this->includePaths = array_merge ( $incPath, $this->includePaths );

		set_error_handler ( array (
				$this->errorHandler,
				"publish"
		) );
		set_exception_handler ( array (
				$this->exceptionHandler,
				"publish"
		) );
		$this->includedClasses = array ();

		$this->classAliasRepository = new ClassAliasRepository ();
		$this->objectFactory = new AbstractFactory ( $this );
	}

	/**
	 *
	 * @param string $fullName
	 *        	Namespace And ClassName
	 * @throws ClassNotFoundException
	 */
	public function includeByClassName($fullName) {
		$foundCheck = false;
		$classPath = str_replace ( "\\", DIRECTORY_SEPARATOR, $fullName ) . ".php";
		foreach ( $this->includePaths as &$includePath ) {
			if (file_exists ( $includePath . '/' . $classPath )) {
				$foundCheck = true;
				include_once $includePath . '/' . $classPath;
				break;
			}
		}

		if (! $foundCheck) {
			throw new ClassNotFoundException ( $fullName . " Not Found" );
		}

		array_push ( $this->includedClasses, $fullName );
	}

	/**
	 *
	 * @param mixed $fullName
	 * @return object
	 */
	public function newInstance(&$fullName) {
		$fullName = $this->findFullClassName ( $fullName );
		return $this->objectFactory->newInstance ( $fullName );
	}
	public function findFullClassName(&$name) {
		return $this->classAliasRepository->findFullClassName ( $name );
	}

	/**
	 * Return a ClassLoader.(Singlton)
	 *
	 * @return \classes\lang\ClassLoader
	 */
	public static function getClassLoader() {
		if (self::$instance == NIL)
			self::$instance = new ClassLoader ();
		return self::$instance = new ClassLoader ();
	}
}
class ErrorHandler {
	public function publish($number, $string, $file = 'Unknown', $line = 0, $context = array()) {
		if (($number == E_NOTICE) || ($number == E_STRICT))
			return false;
		if (! error_reporting ())
			return false;

			// FIXME ErrorHandler 에러 페이지 처리 필요
		return true;
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
		header ( 'Content-Type: text/xml; charset=UTF-8' );
		$ref = new \ReflectionObject ( $exception );
		$exceptionName = $ref->getName ();

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

			// [file] =>
			// E:\Zend\SolarMonitor\solarmonitor\classes\service\ApartmentServiceImpl.php
			// [line] => 44
			// [function] => getLatest
			// [class] =>
			// solarmonitor\classes\repository\apartment\ApartmentLogRepository
			// [type] => ->
			// [args] => Array

			$strBuilder->append ( sprintf ( self::TRACE, @$traceException ['class'], @$traceException ['function'], @$traceException ['line'], @$traceException ['file'], $argsBuilder->toString () ) );
		}

		printf ( self::XML_WRAPPER, $exceptionName, $exception->getMessage (), $exception->getFile (), $exception->getCode (), $exception->getLine (), $strBuilder->toString () );
	}
	private function argmentsToXml($args) {
	}
}
