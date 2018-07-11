<?php
namespace classes\exception;
class AssertCallerException extends \RuntimeException {
	public function __construct($actualCallerName, $expectedCallerName, $code = 0, \Exception $previous = null) {
		$message = "Expected Caller Name is {$expectedCallerName}. but Actual Caller name is {$actualCallerName} ";
		parent::__construct($message, $code, $previous);
	}
}
