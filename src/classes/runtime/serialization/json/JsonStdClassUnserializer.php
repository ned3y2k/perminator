<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-15
 * Time: 오전 4:47
 */

namespace classes\runtime\serialization\json;


use classes\runtime\ClassReflectionPool;

class JsonStdClassUnserializer {
	public static function isJsonUnserializableStdClass($data) {
		return $data instanceof \stdClass
			&& property_exists($data, '__class')
			&& in_array('classes\runtime\serialization\json\IJsonUnserializable', class_implements($data->__class))
		;
	}

	/**
	 * @param \stdClass $data
	 * @return IJsonUnserializable
	 * @throws \ReflectionException
	 */
	public static function createInstance(\stdClass $data) {
		/** @var IJsonUnserializable $instance */
		$instance = self::getClassReflection($data->__class)->newInstanceWithoutConstructor();
		$instance->jsonUnserialize($data);
		return $instance;
	}

	/**
	 * @param $className
	 * @return \ReflectionClass
	 * @throws \ReflectionException
	 */
	private static function getClassReflection($className) {
		return ClassReflectionPool::getClassReflection($className);
	}

}