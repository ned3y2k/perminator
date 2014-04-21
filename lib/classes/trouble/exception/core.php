<?php
namespace classes\trouble\exception\core;

use classes\context\Context;
class PerminatorPHPErrorException extends \ErrorException {
	/**
	 * @var Context
	 */
	private $context;

	function __construct($message, $code, $file, $line, Context $context = null) {
		$this->message = $message;
		$this->code = $code;
		$this->file = $file;
		$this->line = $line;
		$this->context = $context;
	}

	/**
	 * @return \classes\content\Context
	 */
	public final function getContext() {
		return $this->context;
	}
}

class WarningException              extends PerminatorPHPErrorException {}
class ParseException                extends PerminatorPHPErrorException {}
class NoticeException               extends PerminatorPHPErrorException {}
class CoreErrorException            extends PerminatorPHPErrorException {}
class CoreWarningException          extends PerminatorPHPErrorException {}
class CompileErrorException         extends PerminatorPHPErrorException {}
class CompileWarningException       extends PerminatorPHPErrorException {}
class UserErrorException            extends PerminatorPHPErrorException {}
class UserWarningException          extends PerminatorPHPErrorException {}
class UserNoticeException           extends PerminatorPHPErrorException {}
class StrictException               extends PerminatorPHPErrorException {}
class RecoverableErrorException     extends PerminatorPHPErrorException {}
class DeprecatedException           extends PerminatorPHPErrorException {}
class UserDeprecatedException       extends PerminatorPHPErrorException {}

class ClassNotFoundException extends PHPScriptNotFoundException {}
class PHPScriptNotFoundException extends \RuntimeException {}

class BeanInitializationException extends \RuntimeException { }
class NotControllerException extends \RuntimeException { }

class HttpResponseException extends \RuntimeException {
	const BAD_REUQEST = 400;
	const UNAUTHORIZED = 401;
	const PAYMENT_REQUIRED = 402;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const NOT_ACCEPTABLE = 406;

	public function __construct($message, $code, $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}