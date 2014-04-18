<?php
namespace classes\trouble\exception\core;

class PerminatorPHPErrorException extends \ErrorException {
	/**
	 * @var Context
	 */
	private $context;

	function __construct($message, $code, $file, $line, \Context $context = null) {
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