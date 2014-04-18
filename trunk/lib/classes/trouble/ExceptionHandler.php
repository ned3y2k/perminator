<?php
namespace classes\trouble;


use conf\Core;
class ExceptionHandler {
	public function publish(\Exception $exception, \Context $context) {
		echo $exception->getMessage();
	}
}
