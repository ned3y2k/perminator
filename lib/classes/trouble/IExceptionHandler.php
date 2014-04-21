<?php
namespace classes\trouble;

use classes\context\Context;
interface IExceptionHandler {
	public function publish(\Exception $exception, Context $context);
}