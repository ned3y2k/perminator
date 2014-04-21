<?php
namespace classes\util;

class ObjectValueInflater {
	const MODE_METHOD = 0;
	const MODE_PROPERTY = 1;

	public static function inflater(array $objects, $invokeName, $invokeArgs = null) {
		if(!is_array($objects) || count($objects) == 0) return null;

		$ref = new \ReflectionObject($objects[0]);
		$mode = $ref->hasMethod($invokeName) ? self::MODE_METHOD : self::MODE_PROPERTY;

		if($mode == self::MODE_METHOD) {
			$values = array();
			$methodRef = $ref->getMethod($invokeName);
			$argsCount = count($methodRef->getParameters());
			unset($methodRef);

			if($argsCount > 0) {
				foreach ($objects as $object) {
					array_push($values, call_user_func_array(array($object, $invokeName), $invokeArgs));
				}
			} else {
				foreach ($objects as $object) {
					array_push($values, $object->$invokeName());
				}
			}

			return $values;
		} elseif($mode == self::MODE_PROPERTY) {
			$values = array();
			foreach ($objects as $object) {
				$value = $object->$invokeName;
				array_push($values, $value);
			}
			return $values;
		} else {
			throw new \InvalidArgumentException('not found invoker');
		}
	}
}