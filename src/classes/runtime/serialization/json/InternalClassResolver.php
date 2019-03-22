<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-02
 * 시간: 오후 10:52
 */

namespace classes\runtime\serialization\json;


/**
 * Class InternalClassResolver
 *
 * @package classes\runtime\serialization\json
 */
class InternalClassResolver {
	/**
	 * @param \DateTime $element
	 * @param \stdClass $out
	 */
	static function resolveDateTime(\DateTime $element, \stdClass $out) {
		$buff = (array)$element;

		$out->date = $buff['date'];
		$out->timezone_type = $buff['timezone_type'];
		$out->timezone = $buff['timezone'];
	}

	/**
	 * ignore Closure type
	 *
	 * @param $val
	 * @param $out
	 */
	static function resolveClosure($val, \stdClass $out) { }
}