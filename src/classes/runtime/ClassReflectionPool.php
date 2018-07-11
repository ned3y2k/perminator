<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-15
 * Time: 오전 4:14
 */

namespace classes\runtime;


class ClassReflectionPool {
	/** @var \ReflectionClass[] */
	private static $reflections = [];

	/**
	 * @param $class
	 * @return \ReflectionClass
	 * @throws \ReflectionException
	 */
	public static function getClassReflection($class): \ReflectionClass {
		if(!array_key_exists($class, self::$reflections))
			self::$reflections[$class] = new \ReflectionClass($class);

		return self::$reflections[$class];
	}
}