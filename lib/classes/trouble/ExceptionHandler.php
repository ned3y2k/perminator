<?php
namespace classes\trouble;

use conf\Core;
use classes\trouble\exception\core\HttpResponseException;
use classes\context\Context;

class ExceptionHandler implements IExceptionHandler {
	public function publish(\Exception $exception, Context $context) {
		$printerName = Core::EXCEPTION_PRINTER;
		$printer = new $printerName();

		/* @var $printer \classes\trouble\printer\IExceptionPrinter */
		if(!DEBUG && $exception instanceof HttpResponseException) {
			header(http_response_code($exception->getCode()));
			// FIXME 여기 처리하도록!
		} else {
			$printer->publish($exception);
		}
	}
}