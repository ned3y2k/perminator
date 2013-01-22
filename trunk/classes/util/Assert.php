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
}