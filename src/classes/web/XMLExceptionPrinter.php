<?php
namespace classes\web;

use classes\exception\error\WarningException;
use classes\lang\ArrayStringBuilder;
use classes\lang\ArrayUtil;
use classes\lang\StringBuilder;

/**
 * Perminator Class
 */
class XMLExceptionPrinter {
	/** xml 루트 포맷 */
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

	/** 자식 포맷 */
	const EXCEPTIONS_WRAPPER_FORMAT = <<< XML
<Exception type="%s">
	<Message><![CDATA[%s]]></Message>
	<File><![CDATA[%s]]></File>
	<Code>%s</Code>
	<Line>%s</Line>
	<ExceptionStacks>
		%s
	</ExceptionStacks>
	<PreviousException>
		%s
	</PreviousException>
</Exception>
XML;

	/** 스택 포맷 */
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

	/** @param \Exception $exception 예외를 xml로 출력 */
	public function publish(\Throwable $exception) {
		$exceptionStacksStrBuilder = new ArrayStringBuilder();
		$callStacksStrBuilder      = new ArrayStringBuilder();

		header_remove();
		header('Content-Type: text/xml; charset=UTF-8', true, 500);
		$exceptionName   = get_class($exception);
		$traceExceptions = $exception->getTrace();
		$callStacks      = debug_backtrace();

		foreach ($traceExceptions as $traceException) {
			$stacksStr = "";
			if (array_key_exists('args', $traceException))
				$stacksStr = $this->argumentsToXml($traceException ['args']);

			$exceptionStacksStrBuilder->append(
				sprintf(
					self::EXCEPTION_STACK_FORMAT,
					ArrayUtil::getValue($traceException, 'class'),
					ArrayUtil::getValue($traceException, 'function'),
					ArrayUtil::getValue($traceException, 'line'),
					ArrayUtil::getValue($traceException, 'file'),
					$stacksStr
				)
			);
		}


		//$callStacks = array_reverse($callStacks);
		foreach ($callStacks as $callStack) {
			$stacksStr = "";
			if (array_key_exists('args', $callStack))
				$stacksStr = $this->argumentsToXml($callStack [ 'args' ]);

			if (!array_key_exists('type', $callStack)) $type = "unknown";
			elseif ($callStack [ 'type' ] == "->") $type = "method";
			elseif ($callStack [ 'type' ] == "::") $type = "static method";
			elseif ($callStack [ 'type' ] == "") $type = "function";
			else throw new \UnsupportedOperationException(__CLASS__.__METHOD__.':'.__LINE__);

			$class = array_key_exists('class', $callStack) ? $callStack [ 'class' ] : 'unknown';

			$callStacksStrBuilder->append(
				sprintf(
					self::CALL_STACK_FORMAT,
					$class,
					$callStack ['function'],
					ArrayUtil::getValue($callStack, 'line'),
					$type,
					ArrayUtil::getValue($callStack, 'file'),
					$stacksStr
				)
			);
		}

		$exceptionTraceStr = sprintf(
			self::EXCEPTIONS_WRAPPER_FORMAT,
			$exceptionName,
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getCode(),
			$exception->getLine(),
			$exceptionStacksStrBuilder->toString(),
			$this->createPreviousString($exception->getPrevious())
		);
		$response = sprintf(self::XML_WRAPPER, $exceptionTraceStr, $callStacksStrBuilder->toString());
		echo $response;

		$this->writeResponse($exception, $response);
		die(-1);
	}

	/**
	 * 이전 예외를 string 으로..
	 * @param \Exception $exception
	 * @return string
	 */
	private function createPreviousString(\Exception $exception = null) {
		if($exception == null) return '';

		$exceptionStacksStrBuilder = new StringBuilder();
		$traceExceptions = $exception->getTrace();

		foreach ($traceExceptions as $traceException) {
			$stacksStr = "";
			if (array_key_exists('args', $traceException))
				$stacksStr = $this->argumentsToXml($traceException ['args']);

			$exceptionStacksStrBuilder->append(
				sprintf(
					self::EXCEPTION_STACK_FORMAT,
					ArrayUtil::getValue($traceException, 'class'),
					ArrayUtil::getValue($traceException, 'function'),
					ArrayUtil::getValue($traceException, 'line'),
					ArrayUtil::getValue($traceException, 'file'),
					$stacksStr
				)
			);
		}


		$previous = '';
		if($exception->getPrevious() != null) {
			$previous = $this->createPreviousString($exception->getPrevious());
		}

		$exceptionTraceStr = sprintf(
			self::EXCEPTIONS_WRAPPER_FORMAT,
			get_class($exception),
			$exception->getMessage(),
			$exception->getFile(),
			$exception->getCode(),
			$exception->getLine(),
			$exceptionStacksStrBuilder->toString(),
			$previous
		);

		return $exceptionTraceStr;
	}

	/**
	 * 인자를 xml string 으로...
	 * @param $args
	 * @return string
	 */
	private function argumentsToXml($args) {
		$argsBuilder = new ArrayStringBuilder ();

		foreach ($args as $index => $arg) {
			try {
				if (is_object($arg)) {
					$ref = new \ReflectionObject ($arg);
					$argsBuilder->append("<Arg name=\"{$index}\" class=\"{$ref->getName()}\">");
					foreach ($arg as $key => $value) {
						if($value === null) { $value = 'null'; }

						if (is_scalar($value)) {
							$argsBuilder->append("<{$key}><![CDATA[");
							$argsBuilder->append($value);
							$argsBuilder->append("]]></{$key}>");
						} else {
							//$exported = var_export($value, true);
							// $argsBuilder->append ( "<field{$key}><![CDATA[" );
							//$argsBuilder->append ( $exported );
							//$argsBuilder->append ( "]]></field{$key}>" );
							// @FIXME 순한참조 문제 해결할것!

							$argsBuilder->append ( "<{$key}>print error</{$key}>\n" );
						}
					}
					$argsBuilder->append("</Arg>\n");
				} elseif (!is_array($arg)) {
					$argsBuilder->append("<Arg name=\"{$index}\"><![CDATA[");
					$argsBuilder->append($this->escapeString($arg));
					$argsBuilder->append("]]></Arg>");
				} elseif (is_array($arg)) {
					$argsBuilder->append("<Args name=\"{$index}\" type=\"array\">");
					// $argsBuilder->append ( $this->argmentsToXml($arg) ); // 요쪽 메모리 에러 주범!
					$argsBuilder->append("</Args>");
				} else {
					echo "XML Printer error 잡을것!";
					$argsBuilder->append ( "<{$index}>print error \n</{$index}>" );
				}
			} catch (WarningException $ex) {
				if (is_scalar($arg)) {
					$argsBuilder->append("<Arg{$index}>\n");
					$argsBuilder->append("<value><![CDATA[");
					$argsBuilder->append($this->escapeString($arg));
					$argsBuilder->append("]]></value>\n");
					$argsBuilder->append("</Arg{$index}>\n");
				} else $argsBuilder->append("<Arg{$index} notScalar='true' />\n");
			}
		}

		return $argsBuilder->toString();
	}

	/**
	 * xml에 출력 되도록 내용 변경
	 * @param string $in
	 * @return string
	 */
	private function escapeString($in) {
		$out = str_replace('<', '&lt;', $in);
		$out = str_replace('>', '&gt;', $out);
		$out = str_replace('&', '&amp;', $out);
		$out = str_replace('\'', '&pos;', $out);
		$out = str_replace('"', '&quot;', $out);

		return $out;
	}

	private function writeResponse(\Throwable $exception, $content) {
		file_put_contents($this->createExceptionLogFileName($exception), $content);
	}

	/**
	 * @param \Exception $exception
	 * @return string
	 */
	private function createExceptionLogFileName(\Throwable $exception) {
		$dir = _DIR_LOG_PHP_EXCEPTION_ . date('Ymd') . DIRECTORY_SEPARATOR;
		if (!file_exists($dir))
			mkdir($dir, 7666, true);

		return $dir.date('His') . '-' . trim(str_replace("\\", '.', get_class($exception)), '.').'response.xml';
	}
}