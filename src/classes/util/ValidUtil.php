<?php
namespace classes\util;

class ValidUtil {
	static function notNull($var, $message = "this argument is required; it must not be null") {
		if ($var === null || $var === null)
			throw new ValidationNullException($message);
	}

	static function arrayNotHasKey($key, array $array, $message) {
		if (!array_key_exists($key, $array))
			throw new ValidationException ($message);
	}

	static function equal($var1, $var2) {
		if (is_object($var1 || $var2))
			return spl_object_hash($var1) == spl_object_hash($var2);
		else
			return $var1 == $var2;
	}

	static function isInt($var) {
		if (!is_int($var)) throw new ValidationInvalidTypeException("is not int type");

		return $var;
	}

	static function fileExist($path) {
		if (!file_exists($path)) throw new ValidationFileNotExistException();
	}
}

class ValidationException extends \InvalidArgumentException {
	function __construct($message = null, $code = null, $previous = null) { parent::__construct($message, $code, $previous); }

	function __toString() {
		if ($this->message === null) return "[Validate failed]";

		return "[Validate failed] - " . $this->message;
	}
}

class ValidationNullException extends ValidationException {
	function __toString() {
		if ($this->message === null) return "[Validate failed-ValueNull]";

		return "[Validate failed-ValueNull] - " . $this->message;
	}
}

class ValidationNotHasKeyException extends ValidationException {
	function __toString() {
		if ($this->message === null) return "[Validate failed-NotHasKey]";

		return "[Validate failed-NotHasKey] - " . $this->message;
	}
}

class ValidationNotEqualException extends ValidationException {
	function __toString() {
		if ($this->message === null) return "[Validate failed-NotEqual]";

		return "[Validate failed-NotEqual] - " . $this->message;
	}
}

class ValidationInvalidTypeException extends ValidationException {
	function __toString() {
		if ($this->message === null) return "[Validate failed-InvalidType]";

		return "[Validate failed-InvalidType] - " . $this->message;
	}
}

class ValidationFileNotExistException extends ValidationException {
	function __toString() {
		if ($this->message === null) return "[Validate failed-InvalidType]";

		return "[Validate failed-FileNotExist] - " . $this->message;
	}
}