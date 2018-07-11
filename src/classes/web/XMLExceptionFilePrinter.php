<?php
namespace classes\web;

use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\FileNotFoundException;
use classes\io\exception\PermissionException;
use classes\lang\ArrayUtil;
use classes\lang\FileStringBuilder;

/**
 * Perminator Class
 */
class XMLExceptionFilePrinter {
	const XML_WRAPPER = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<XMLExceptionPrinter ver="0.1">
	<TraceException>
%s
	</TraceException>
	<TraceCallStack>
		%s
	</TraceCallStack>
</XMLExceptionPrinter>
XML;

	const EXCEPTIONS_WRAPPER_FORMAT = <<< XML
<Exception type="%s">
	<Message><![CDATA[%s]]></Message>
	<File><![CDATA[%s]]></File>
	<Code>%s</Code>
	<Line>%s</Line>
	<ExceptionStacks>
		%s
	</ExceptionStacks>
</Exception>
XML;
	const EXCEPTION_STACK_FORMAT    = <<< XML
<ExceptionStack Class="%s" Function="%s" Line="%s">
	<File><![CDATA[%s]]></File>
	<Args>%s</Args>
</ExceptionStack>
XML;

	const CALL_STACK_FORMAT = <<< XML
<CallStack Class="%s" Function="%s" Line="%s" type="%s">
	<File>%s</File>
	<args>%s</args>
</CallStack>
XML;

	/**
	 * @param \Exception $exception
	 * @throws DirectoryNotFoundException
	 * @throws FileNotFoundException
	 * @throws PermissionException
	 */
	public function publish(\Exception $exception) {
		$exceptionStacksStrBuilder = new FileStringBuilder();
		$callStacksStrBuilder      = new FileStringBuilder();

		getApplicationContext()->getResponseContext()
			->headerRemove()
			->putRawHeader('Content-Type: text/xml; charset=UTF-8', true, 500);

		$exceptionName   = get_class($exception);
		$traceExceptions = $exception->getTrace();
		$callStacks      = debug_backtrace();

		foreach ($traceExceptions as $traceException) {
			$stacksStr = "";
			if (array_key_exists('args', $traceException)) $stacksStr = $this->argumentsToXml($traceException [ 'args' ]);

			$exceptionStacksStrBuilder->append(sprintf(self::EXCEPTION_STACK_FORMAT, ArrayUtil::getValue($traceException, 'class'), ArrayUtil::getValue($traceException, 'function'), ArrayUtil::getValue($traceException, 'line'), ArrayUtil::getValue($traceException, 'file'), $stacksStr));
		}


		//$callStacks = array_reverse($callStacks);
		foreach ($callStacks as $callStack) {
			$stacksStr = "";
			if (array_key_exists('args', $callStack)) $stacksStr = $this->argumentsToXml($callStack [ 'args' ]);

			$type = "unknown";
			if ($callStack [ 'type' ] == "->") $type = "method";
			elseif ($callStack [ 'type' ] == "::") $type = "static method";
			elseif ($callStack [ 'type' ] == "") $type = "function";

			$class = array_key_exists('class', $callStack) ? $callStack [ 'class' ] : 'unknown';

			$callStacksStrBuilder->append(sprintf(self::CALL_STACK_FORMAT, $class, $callStack [ 'function' ], ArrayUtil::getValue($callStack, 'line'), $type, ArrayUtil::getValue($callStack, 'file'), $stacksStr));
		}

		$exceptionTraceStr = sprintf(self::EXCEPTIONS_WRAPPER_FORMAT, $exceptionName, $exception->getMessage(), $exception->getFile(), $exception->getCode(), $exception->getLine(), $exceptionStacksStrBuilder->toString());
		printf(self::XML_WRAPPER, $exceptionTraceStr, $callStacksStrBuilder->toString());

		exit;
	}

	/**
	 * @param $args
	 * @return string
	 * @throws DirectoryNotFoundException
	 * @throws FileNotFoundException
	 * @throws PermissionException
	 */
	private function argumentsToXml($args) {
		$argsBuilder = new FileStringBuilder ();

		foreach ($args as $index => $arg) {
			try {
				if (is_object($arg)) {
					$ref = new \ReflectionObject ($arg);
					$argsBuilder->append("<Arg name=\"{$index}\" Class=\"{$ref->getName()}\">");
					foreach ($arg as $key => $value) {
						if (is_scalar($value)) {
							$argsBuilder->append("<{$key}><![CDATA[");
							$argsBuilder->append($value);
							$argsBuilder->append("]]></{$key}>");
						} else {
							$argsBuilder->append ( "print error" );
							//$exported = var_export($value, true);
							// $argsBuilder->append ( "<field{$key}><![CDATA[" );
							//$argsBuilder->append ( $exported );
							//$argsBuilder->append ( "]]></field{$key}>" );
							// @FIXME 순한참조 문제 해결할것!
						}
					}
					$argsBuilder->append("</Arg>\n");
				} elseif (!is_array($arg)) {
					$argsBuilder->append("<Arg name=\"{$index}\"><![CDATA[");
					$argsBuilder->append($arg);
					$argsBuilder->append("]]></Arg>");
				} elseif (is_array($arg)) {
					$argsBuilder->append("<Args name=\"{$index}\">");
					$argsBuilder->append($this->argumentsToXml($arg));
					$argsBuilder->append("</Args>");
				} else {
					$argsBuilder->append ( "print error" );
					echo "XML Printer error 잡을것!";
				}
			} catch (\WarningException $ex) {
				if (is_scalar($arg)) {
					$argsBuilder->append("<Arg{$index}>\n");
					$argsBuilder->append("<value><![CDATA[");
					$argsBuilder->append($arg);
					$argsBuilder->append("]]></value>\n");
					$argsBuilder->append("</Arg{$index}>\n");
				} else $argsBuilder->append("<Arg{$index} notScalar='true' />\n");
			}
		}

		return $argsBuilder->toString();
	}
}