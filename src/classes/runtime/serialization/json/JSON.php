<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-02
 * 시간: 오후 10:52
 */

namespace classes\runtime\serialization\json;

class JSON {
	/** @var \Closure[] */
	private static $iterators = null;

	/**
	 * @param       $object
	 * @param array $iterators
	 *
	 * @return string
	 * @throws \ReflectionException
	 */
	public static function encode($object, array $iterators = null) {
		self::$iterators = $iterators;

		return json_encode(self::transferAllPropertyInstance($object, new JSONSerializeContext()));
	}

	/**
	 * @param mixed $element
	 * @param JSONSerializeContext $context
	 *
	 * @return array|\stdClass
	 * @throws \ReflectionException
	 */
	private static function transferAllPropertyInstance($element, JSONSerializeContext $context) {
		if (is_object($element)) {
			return self::transferObjectProperties($element, $context);
		} elseif (is_array($element)) {
			return self::transferArrayElements($element, $context);
		}

		return $element;
	}

	/**
	 * @param                      $element
	 * @param JSONSerializeContext $context
	 *
	 * @return null|\stdClass
	 * @throws \ReflectionException
	 */
	private static function transferObjectProperties($element, JSONSerializeContext $context) {
		$newElement          = new \stdClass();
		$newElement->__class = get_class($element);

		if ($element instanceof JsonSerializable) {
			$element->getSerializeConfig($newElement);
		} elseif ($context->isInternal($newElement->__class)) {
			$typeName = 'resolve' . ucfirst($newElement->__class);
			if (!$element instanceof \stdClass) {
				if (!method_exists('\classes\runtime\serialization\json\InternalClassResolver', $typeName)) {
					throw new \UnsupportedOperationException('Serialize Unsupported Internal Type. ' . $newElement->__class);
				} else {
					InternalClassResolver::$typeName($element, $newElement);
				}
			}
		}

		if ($element instanceof \stdClass) {
			$array = (array)$element;
			if (count($array) == 0) {
				$newElement = null;
			} else {
				foreach ($array as $propertyName => $propertyValue) {
					if (!is_object($newElement)) {
						throw new \RuntimeException("error" . var_export($newElement, true) . var_export($element, true));
					}

					if (self::ignoreObjectField($context, $newElement, $propertyName))
						continue;

					$newElement->$propertyName = self::extractElement($element, $propertyName, $propertyValue, $context);
				}
			}
		} else {
			foreach ($context->getPropertyReflectors($newElement->__class) as $property) {
				$propertyName = $property->name ? $property->name : $property->getName();

				if (self::ignoreObjectField($context, $newElement, $propertyName))
					continue;

				$propertyValue             = $property->getValue($element);
				$newElement->$propertyName = self::extractElement($element, $propertyName, $propertyValue, $context);
			}
		}

		return $newElement;
	}

	/**
	 * @param                      $element
	 * @param JSONSerializeContext $context
	 *
	 * @return array
	 * @throws \ReflectionException
	 */
	private static function transferArrayElements($element, JSONSerializeContext $context) {
		$array = array();

		foreach ($element as $elementName => $elementValue) {
			$elementValue = self::extractElement($element, $elementName, $elementValue, $context);
			if ($elementValue instanceof IJsonIgnoreField) continue;

			$array[$elementName] = $elementValue;
		}

		return $array;
	}

	/**
	 * @param mixed $elementContainer
	 * @param string $elementName
	 * @param mixed $elementValue
	 * @param JSONSerializeContext $context
	 *
	 * @return mixed|\stdClass
	 * @throws \ReflectionException
	 */
	private static function extractElement($elementContainer, $elementName, $elementValue, JSONSerializeContext $context) {
		if (is_object($elementValue) || is_array($elementValue)) {
			return self::userExtractValue($context->getUserExtractorContext(), $elementContainer, $elementName, self::transferAllPropertyInstance($elementValue, $context));
		} elseif (is_scalar($elementValue)) {
			return self::userExtractValue($context->getUserExtractorContext(), $elementContainer, $elementName, $elementValue);
		} elseif ($elementValue === null) {
			return $elementValue;
		} else {
			throw new \UnsupportedOperationException(gettype($elementValue));
		}
	}

	/**
	 * @param UserExtractorContext $userExtractorContext
	 * @param mixed $owner
	 * @param string $name
	 * @param mixed $value
	 *
	 * @return mixed
	 * @throws \ReflectionException
	 */
	private static function userExtractValue(UserExtractorContext $userExtractorContext, $owner, $name, $value) {
		if (self::$iterators != null && array_key_exists($name, self::$iterators)) {
			$func = self::$iterators[$name];

			$comparator = $userExtractorContext->getComparator($name, $func);
			if ($comparator($owner)) {
				return $func($owner, $value);
			} else {
				return $value;
			}
		} else {
			return $value;
		}
	}

	/**
	 * @param JSONSerializeContext $context
	 * @param  mixed $newElement
	 * @param  string $propertyName
	 *
	 * @return bool
	 * @throws \ReflectionException
	 */
	private static function ignoreObjectField(JSONSerializeContext $context, $newElement, $propertyName) {
		if (property_exists($newElement, $propertyName) && $newElement->$propertyName instanceof IJsonIgnoreField) {
			unset($newElement->$propertyName);

			return true;
		} elseif (property_exists($newElement, $propertyName) && is_scalar($newElement->$propertyName)) {
			return true;
		} elseif (property_exists($newElement, $propertyName) && !is_scalar($newElement->$propertyName)) {
			$newElement->$propertyName = self::extractElement($newElement, $propertyName, $newElement->$propertyName, $context);

			return true;
		}

		return false;
	}

	/**
	 * @param string|null $data
	 * @return IJsonUnserializable|mixed|null
	 * @throws \ReflectionException
	 */
	public static function decode(string $data = null) {
		if (!$data)
			return null;

		$buff         = json_decode($data);
		switch (json_last_error()) {
			case JSON_ERROR_DEPTH:
				throw new JsonDecodeException(JSON_ERROR_DEPTH, $data);
				break;
			case JSON_ERROR_CTRL_CHAR:
				throw new JsonDecodeException(JSON_ERROR_CTRL_CHAR, $data);
				break;
			case JSON_ERROR_SYNTAX;
				throw new JsonDecodeException(JSON_ERROR_SYNTAX, $data);
				break;
		}

		$rootIsObject = array_key_exists('__class', $buff);
		if ($rootIsObject) {
			if (JsonStdClassUnserializer::isJsonUnserializableStdClass($buff))
				return JsonStdClassUnserializer::createInstance($buff);
		}

		return $buff;
	}


}