<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 3:50
 */

namespace classes\lang;


class ArrayUtil {
	/**
	 * 다차원 배열에 해당 하는 키가 존재하는지 검사한다.
	 *
	 * <code>
	 *        $array = [
	 *              'charly'=>['bag'=>
	 *                  ['money'=>'1000', 'creditCard'=>'']
	 *               ],
	 *              'delta'=>['backpack'=>'notebook'],
	 *              'prank'=>['shoulderBag'=>'none'],
	 *        ];
	 *        echo array_element_select($array, array('charly', 'bag', 'creditCard'));
	 * </code>
	 *
	 * @param array|null $array
	 * @param array      $selectors
	 *
	 * @return bool
	 */
	public static function existsMultiDimensionalKey(array $array = null, array $selectors) {
		if(!$array && !$selectors)
			return true;
		else if(!$array && $selectors)
			return false;
		else if($array && !$selectors)
			throw new \InvalidArgumentException('Array is not empty, but selector is empty');

		$selected = $array;
		foreach ($selectors as $selector) {
			if(!is_scalar($selector))
				throw new \InvalidArgumentException('selectors contains a non-scalar selector');

			if(is_array($selected) && array_key_exists($selector, $selected))
				$selected = $selected[$selector];
			else
				return false;
		}

		return true;
	}

	/**
	 * 배열이 순환성이 있는지 검사한다.
	 *
	 * @param array $array
	 *
	 * @return bool
	 */
	public static function isRecursive(array $array) {
		$some_reference = new \stdclass();
		return self::recursiveIteration($array, $some_reference);
	}

	/**
	 * 배열 재귀 탐색
	 *
	 * @param array $array
	 * @param       $reference
	 *
	 * @return bool
	 */
	public static function recursiveIteration(array & $array, $reference) {
		$last_element = end($array);
		if ($reference === $last_element) {
			return true;
		}
		$array[] = $reference;

		foreach ($array as &$element) {
			if (is_array($element)) {
				if (self::recursiveIteration($element, $reference)) {
					self::removeRecursiveDependency($array, $reference);
					return true;
				}
			}
		}

		self::removeRecursiveDependency($array, $reference);

		return false;
	}

	/**
	 * 배열의 순환성을 제거한다.
	 *
	 * @param array $array
	 * @param       $reference
	 */
	public static function removeRecursiveDependency(array & $array, $reference) {
		if (end($array) === $reference) {
			unset($array[key($array)]);
		}
	}

	/**
	 * 배열을 복사한다.
	 *
	 * @param array $array
	 *
	 * @return array
	 */
	public static function copy(array $array) {
		$newArray = array();

		foreach ($array as $key => $element) {
			$newArray[$key] = is_object($element) ? clone $element : self::primitiveValueCopy($element);
		}

		return $newArray;
	}

	/**
	 * 배열 안에 있는 값을 리턴 (없다면 기본값 리턴)
	 *
	 * @param array $array
	 * @param mixed $index
	 * @param null  $default
	 *
	 * @return mixed
	 */
	public static function getValue($array, $index, $default = null) {
		if (!is_array($array) || count($array) == 0 || !array_key_exists($index, $array)) return $default;
		return $array [$index];
	}

	/**
	 * 해당 배열이 연관 배열인지 확인한다.
	 *
	 * @param array $arr
	 *
	 * @return bool
	 */
	public static function isAssociate(array $arr) {
		return array_keys($arr) !== range(0, count($arr) - 1);
	}

	/**
	 * Array 안에 있는 내용을 선택한다.
	 *
	 * <code>
	 *        $a["b"]["c"]["d"]="1234";
	 *        echo array_element_select($a, array('b','c','d'));
	 * </code>
	 *
	 * @param  array $array
	 * @param  array $selectors
	 * @param  mixed $default
	 *
	 * @return mixed
	 */
	public static function select(array $array = null, array $selectors, $default = null) {
		if ($array == null) return $default;

		$selected = $array;
		foreach ($selectors as $selector) {
			if (!is_array($selected))
				throw new \InvalidArgumentException('array in object');

			if (!array_key_exists($selector, $selected))
				return (is_callable($default) && !is_string($default)) ? call_user_func($default) : $default;

			if (is_object($selector) && !ObjectUtil::hasToString($selector))
				throw new \InvalidArgumentException('Invalid Selector');

			$selected = $selected[(string)$selector];
		}

		return $selected;
	}

	private static function primitiveValueCopy($value) {
		$holder = new PrimitiveValueHolder($value);
		$newHolder = clone $holder;

		return $newHolder->getValue();
	}
}

class PrimitiveValueHolder {
	private $value;

	function __construct($value) {
		$this->value = $value;
	}

	function getValue() {
		return $this->value;
	}
}
