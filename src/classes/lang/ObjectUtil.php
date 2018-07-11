<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 2:53
 */

namespace classes\lang;


class ObjectUtil {
	/**
	 * 필드에 엑세스 하는 클로져 생성
	 *
	 * @param string $name
	 *
	 * @return \Closure
	 */
	public static function createFieldGetter($name) {
		/**
		 * @param object $instance
		 *
		 * @return mixed
		 */
		return function ($instance) use ($name) {
			return $instance == null ? null : $instance->$name;
		};
	}

	/**
	 * 메서드에 엑세스 하는 클로져 생성
	 *
	 * @param $name
	 *
	 * @return \Closure
	 */
	public static function createMethodExecutor($name) {
		/**
		 * @param object $instance
		 *
		 * @return mixed
		 */
		return function ($instance) use ($name) {
			return $instance == null ? null : $instance->$name();
		};
	}

	/**
	 * 배열을 객체로 만들어준다.
	 *
	 * @param array $arg
	 * @param string $className
	 *
	 * @return null|object
	 * @throws \ReflectionException
	 */
	function convert_to_object(array $arg = null, string $className) {
		if (is_null($arg)) {
			return null;
		}

		$reflection = new \ReflectionClass($className);
		$instance = $reflection->newInstance();

		foreach ($reflection->getProperties() as $var) {
			$var->setAccessible(true);
			$key = $var->getName();

			if (isset($arg[$key])) {
				$var->setValue($instance, $arg[$key]);
			}
		}

		foreach ($reflection->getMethods() as $method) {
			$methodName = $method->getName();

			if (StringUtil::startsWith($methodName, 'get')) {
				$instance->$methodName = $method->invoke($instance);
			}
		}

		return $instance;
	}

	/**
	 * 인스턴스의 필드를 배열로 변환
	 * FIXME get_object_vars 요거랑 비슷?
	 *
	 * @param string $data
	 *
	 * @return array|string
	 */
	public static function toArray($data) {
		if (is_array($data) || is_object($data)) {
			$result = array();
			foreach ($data as $key => $value) {
				$result [$key] = self::toArray($value);
			}

			return $result;
		}

		return $data;
	}

	public static function setPrivateField($object, $name, $value) {
		if (!is_object($object)) {
			throw new \InvalidArgumentException('not object');
		}
		if ($name == null) {
			throw new \InvalidArgumentException('name is null');
		}

		$ref = new \ReflectionObject($object);

		if (!$ref->hasProperty($name)) {
			$cRef = $ref->getParentClass();
			if ($cRef == null) {
				throw new \InvalidArgumentException("not found \"{$name}\" property in " . get_class($object));
			}

			while (!$cRef->hasProperty($name)) {
				$cRef = $cRef->getParentClass();
			}

			$refP = $cRef->getProperty($name);
		} else {
			$refP = $ref->getProperty($name);
		}

		$refP->setAccessible(true);
		$refP->setValue($object, $value);
	}

	public static function getPrivateField($object, $name) {
		if (!is_object($object)) {
			throw new \InvalidArgumentException('not object');
		}
		if ($name == null) {
			throw new \InvalidArgumentException('name is null');
		}

		$ref = new \ReflectionObject($object);

		if (!$ref->hasProperty($name)) {
			$cRef = $ref->getParentClass();
			while (!$cRef->hasProperty($name)) {
				$cRef = $cRef->getParentClass();
			}

			$refP = $cRef->getProperty($name);
		} else {
			$refP = $ref->getProperty($name);
		}

		$refP->setAccessible(true);

		return $refP->getValue($object);
	}

	/**
	 * 객체가 __toString 메서드를 가지고 있는지 확인
	 *
	 * @param \stdClass $item
	 *
	 * @return bool
	 */
	public static function hasToString($item = null) {
		return is_object($item) && method_exists($item, '__toString');
	}

	/**
	 * 객체 안에 있는 내용을 선택한다.
	 *
	 * <code>
	 *        $a = new MockObject();
	 *        echo ObjectUtil::elementSelect($a, array('getMember','getPartner','name'));
	 * </code>
	 *
	 * @param  object $object
	 * @param  array  $selectors
	 * @param  mixed  $default
	 *
	 * @throws \InvalidArgumentException
	 * @return mixed
	 */
	public static function select($object, $selectors, $default = null) {
		if ($object == null) {
			return $default;
		} else if (!is_object($object)) {
			throw new \InvalidArgumentException(gettype($object));
		}

		if (is_string($selectors)) {
			$selectors = array($selectors);
		}

		$temp = $object;

		foreach ($selectors as $selector) {
			if (is_object($temp)) {
				if (method_exists($temp, $selector)) {
					$temp = $temp->$selector();
				} elseif (property_exists(get_class($temp), $selector) || isset($temp->$selector)) {
					$temp = $temp->$selector;
				} else {
					return $default;
				}
			} else {
				return $default;
			}
		}

		return $temp;
	}

	/**
	 * 필드를 복사한다.
	 * @param object $origInstance 원본 인스턴스
	 * @param object $destInstance 대상 인스턴스
	 */
	public static function fieldCopy($origInstance, $destInstance) {
		if (get_class($origInstance) && get_class($destInstance)) {
			$ref = new \ReflectionObject ($origInstance);
			$props = $ref->getProperties();

			foreach ($props as $prop) {
				/* @var $prop \ReflectionProperty */
				$isPublic = $prop->isPublic();
				if (!$isPublic) {
					$prop->setAccessible(true);
				}
				$name = $prop->getName();
				$origInstance->$name = $destInstance->$name;
				if (!$isPublic) {
					$prop->setAccessible(false);
				}
			}
		} else {
			throw new \InvalidArgumentException ('instances not matched');
		}
	}
}