<?php
namespace classes\exception;

class AssertEqualException extends \RuntimeException {
	public function __construct($actual, $expected, $code = 0, \Exception $previous = null) {
		$message = "your expect val is {$expected}. but actual val is {$actual}";
		parent::__construct($message, $code, $previous);
	}
}
