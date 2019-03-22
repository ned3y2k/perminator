<?php
namespace classes\exception\http;

class HTTPResponseException extends \RuntimeException {
	public function __construct($message = "", $code = 500, $previous = null) {
		parent::__construct ( $message, $code, $previous );
	}
}