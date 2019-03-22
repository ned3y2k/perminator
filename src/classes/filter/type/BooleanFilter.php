<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 2
 * 시간: 오후 10:40
 */
namespace classes\filter\type;

use classes\filter\IFilter;

class BooleanFilter implements IFilter {

	/**
	 * @param mixed $value
	 *
	 * @return mixed
	 */
	function doFilter($value) {
		if($value == null) return false;

		$value = trim($value);

		if(strlen($value) == 0) return false;
		elseif($value == '0') return false;
		elseif($value == 'false') return false;

		return true;
	}
}