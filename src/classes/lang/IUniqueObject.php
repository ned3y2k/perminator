<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-01
 * 시간: 오후 11:42
 */

namespace classes\lang;


/**
 * Interface IUniqueObject
 *
 * @package classes\lang
 */
interface IUniqueObject {
	/** @return string */
	function hashCode();
}