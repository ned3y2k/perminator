<?php
namespace classes\trouble\exception\assert;

class AssertCallerException extends \RuntimeException {
	public function __construct($actualCallerName, $expectedCallerName, $code = 0, \Exception $previous = null) {
		$message = "Expected Caller Name is {$expectedCallerName}. but Actual Caller name is {$actualCallerName} ";
		parent::__construct($message, $code, $previous);
	}
}

class AssertEqualException extends \RuntimeException {
	public function __construct($actual, $expected, $code = 0, \Exception $previous = null) {
		$message = "your expect val is {$expected}. but actual val is {$actual}";
		parent::__construct($message, $code, $previous);
	}
}
