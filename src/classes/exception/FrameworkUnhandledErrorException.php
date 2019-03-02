<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-10
 * Time: 오전 10:42
 */

namespace classes\exception;

class FrameworkUnhandledErrorException extends \Exception {
	private $args;

	public function __construct($errno, $errstr, $errfile, $errline, $args) {
		parent::__construct(null, null, null);
		$this->args = $args;
	}
}
