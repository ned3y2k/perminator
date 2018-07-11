<?php
namespace classes\io\exception;

use Exception;

class IOException extends \Exception {
	const CODE_UNKNOWN = -1;
}

class PermissionException extends IOException {}
class FileNotFoundException extends IOException {
	public function __construct($message = "", $code = 0, Exception $previous = null)
	{
		if(strlen($message) > 0)
			parent::__construct($message . " not found", $code, $previous);
		else parent::__construct($message . "", $code, $previous);
	}

}
class DirectoryNotFoundException extends IOException {}