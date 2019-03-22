<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-03-02
 * 시간: 오전 9:11
 */

namespace classes\lang;

/**
 * Interface IStringBuilder
 *
 * @package classes\lang
 * Perminator Class
 */
interface IStringBuilder {
	/**
	 * @param string $str
	 * @return IStringBuilder
	 */
	public function append($str);

	/** @return string */
	public function toString(): string;

	/** @return string */
	public function __toString(): string;
}