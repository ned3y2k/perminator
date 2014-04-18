<?php
namespace classes\util;

class Assert {
	static function notNull($object, $message = "[Assertion failed] - this argument is required; it must not be null") {
		if(is_null($object) || (is_string($object) && $object == ""))
			throw new \InvalidArgumentException(message);
	}

	static function arrayNotHasKey($key, array $array, $message) {
		if(!array_key_exists($key, $array))
			throw new \InvalidArgumentException($message);
	}

	static function equal($var1, $var2) {
		if(is_object($var1 || $var2))
			return spl_object_hash($var1) == spl_object_hash($var2);
		else
			return $var1 == $var2;
	}
}