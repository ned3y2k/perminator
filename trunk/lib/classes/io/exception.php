<?php
namespace classes\io\exception;

class IOException extends \Exception {
	const CODE_UNKNOWN = -1;
	const CODE_EXIST_FILE = 1;
	const CODE_NOT_EXIST_FILE = 2;
	const CODE_NOT_VALID = 3;
}

class PermissionException extends IOException {}
class FileNotFoundException extends IOException {}
class DirectoryNotFoundException extends IOException {}