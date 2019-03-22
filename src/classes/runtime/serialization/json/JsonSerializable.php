<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-02
 * 시간: 오후 10:53
 */

namespace classes\runtime\serialization\json;


/**
 * Interface JsonSerializable
 *
 * @package classes\runtime\serialization\json
 */
interface JsonSerializable {
	/**
	 * @param \stdClass $out
	 * @return void
	 */
	public function getSerializeConfig(\stdClass &$out);
}