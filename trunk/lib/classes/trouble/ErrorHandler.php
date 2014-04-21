<?php
namespace classes\trouble;

use classes\trouble\exception\core\PerminatorPHPErrorException;
use classes\trouble\exception\core\WarningException;
use classes\trouble\exception\core\ParseException;
use classes\trouble\exception\core\NoticeException;
use classes\trouble\exception\core\CoreErrorException;
use classes\trouble\exception\core\CoreWarningException;
use classes\trouble\exception\core\CompileErrorException;
use classes\trouble\exception\core\CompileWarningException;
use classes\trouble\exception\core\UserErrorException;
use classes\trouble\exception\core\UserWarningException;
use classes\trouble\exception\core\UserNoticeException;
use classes\trouble\exception\core\StrictException;
use classes\trouble\exception\core\RecoverableErrorException;
use classes\trouble\exception\core\DeprecatedException;
use classes\trouble\exception\core\UserDeprecatedException;
use classes\context\Context;


class ErrorHandler {
	private $phpErrors = array();

	public function publish($code, $message, $file, $line, Context $context = null) {

		// error was suppressed with the @-operator
		if (0 === error_reporting()) { return false;}
		switch($code)
		{
			case E_ERROR:               throw new PerminatorPHPErrorException ($message, $code, $file, $line, $context);
			case E_WARNING:             throw new WarningException            ($message, $code, $file, $line, $context);
			case E_PARSE:               throw new ParseException              ($message, $code, $file, $line, $context);
			case E_NOTICE:              throw new NoticeException             ($message, $code, $file, $line, $context);
			case E_CORE_ERROR:          throw new CoreErrorException          ($message, $code, $file, $line, $context);
			case E_CORE_WARNING:        throw new CoreWarningException        ($message, $code, $file, $line, $context);
			case E_COMPILE_ERROR:       throw new CompileErrorException       ($message, $code, $file, $line, $context);
			case E_COMPILE_WARNING:     throw new CompileWarningException     ($message, $code, $file, $line, $context);
			case E_USER_ERROR:          throw new UserErrorException          ($message, $code, $file, $line, $context);
			case E_USER_WARNING:        throw new UserWarningException        ($message, $code, $file, $line, $context);
			case E_USER_NOTICE:         throw new UserNoticeException         ($message, $code, $file, $line, $context);
			case E_STRICT:              throw new StrictException             ($message, $code, $file, $line, $context);
			case E_RECOVERABLE_ERROR:   throw new RecoverableErrorException   ($message, $code, $file, $line, $context);
			case E_DEPRECATED:          throw new DeprecatedException         ($message, $code, $file, $line, $context);
			case E_USER_DEPRECATED:     throw new UserDeprecatedException     ($message, $code, $file, $line, $context);
		}


		// @FIXME Fatal Error Capture XML로 출력되게 바꿀것!
		// header ( 'Content-Type: text/xml; charset=UTF-8', true, 500 );
		return true; // FIXME FALSE가 되면 어떻게 되나요?ㅋㄷ
	}
}