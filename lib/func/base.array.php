<?php
/**
 * 배열 안에 있는 값을 리턴
 * @param array $array
 * @param mixed $index
 */
function array_value($array, $index, $default = null) {
	if(!is_array($array) || count($array) == 0 || !array_key_exists($index, $array)) return $default;
	return @$array [$index];
}

/**
 * Array 안에 있는 내용을 선택한다.
 *
 * <code>
 * 		$a["b"]["c"]["d"]="1234";
 *		echo array_element_select($a, array('b','c','d'));
 * </code>
 *
 * @param  array   $array
 * @param  array   $selectors
 * @param  mixed   $default
 * @return mixed
 */
function array_element_select($array, array $selectors, $default = null)
{
	foreach ($selectors as $selector)
	{
		if ( ! array_key_exists($selector, $array))
			return (is_callable($default) && ! is_string($default)) ? call_user_func($default) : $default;

		$array = $array[$selector];
	}

	return $array;
}