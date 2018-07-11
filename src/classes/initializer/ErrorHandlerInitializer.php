<?php

namespace classes\initializer;

use classes\exception\error\{
	CompileErrorException,
	CoreErrorException,
	CoreWarningException,
	DeprecatedException,
	NoticeException,
	ParseException,
	RecoverableErrorException,
	StrictException,
	UserDeprecatedException,
	UserErrorException,
	UserNoticeException,
	UserWarningException,
	WarningException
};
use classes\handler\throwable\DefaultThrowableHandler;
use classes\test\handler\BitTestCaseThrowableHandler;
use ErrorException;

class ErrorHandlerInitializer implements Initializer {

	public function init() {
		$this->setExceptionHandler();
		$this->setDefaultErrorHandler();
	}

	private function setExceptionHandler() {
		$exceptionHandler = $this->createExceptionHandler();
		getApplicationContext()->setExceptionHandler($exceptionHandler);
		set_exception_handler(array($exceptionHandler, 'handling'));
	}

	private function createExceptionHandler() {
		if (!TEST) {
			return new DefaultThrowableHandler();
//			/** @noinspection PhpIncludeInspection */
		} else {
			return new BitTestCaseThrowableHandler();
		}
	}

	private function setDefaultErrorHandler() {
		if (!TEST)
			set_error_handler(
				function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
					// error was suppressed with the @-operator
					if (0 === error_reporting()) {
						return false;
					}
					switch ($err_severity) {
						case E_ERROR:
							throw new ErrorException            ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_WARNING:
							throw new WarningException          ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_PARSE:
							throw new ParseException            ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_NOTICE:
							throw new NoticeException           ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_CORE_ERROR:
							throw new CoreErrorException        ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_CORE_WARNING:
							throw new CoreWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_COMPILE_ERROR:
							throw new CompileErrorException     ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_COMPILE_WARNING:
							throw new CoreWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_USER_ERROR:
							throw new UserErrorException        ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_USER_WARNING:
							throw new UserWarningException      ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_USER_NOTICE:
							throw new UserNoticeException       ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_STRICT:
							throw new StrictException           ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_RECOVERABLE_ERROR:
							throw new RecoverableErrorException ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_DEPRECATED:
							throw new DeprecatedException       ($err_msg, 0, $err_severity, $err_file, $err_line);
						case E_USER_DEPRECATED:
							throw new UserDeprecatedException   ($err_msg, 0, $err_severity, $err_file, $err_line);
					}

					throw new \InvalidArgumentException("unknown err_severity: {$err_severity}");
				}
			);
	}
}