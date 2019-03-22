<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 14. 9. 16
 * 시간: 오후 12:00
 */

namespace classes\lang;


/**
 * Class StringBuilder
 *
 * @package classes\lang
 * Perminator Class
 */
class StringBuilder implements IStringBuilder {
	/** @var string 임시저장 버퍼 */
	private $buffer = '';

	/**
	 * @param string $str
	 *
	 * @return IStringBuilder
	 * @throws \InvalidArgumentException
	 */
	public function append($str) {
		if (!is_string($str) && $str instanceof self) throw new \InvalidArgumentException();
		$this->buffer .= $str;
		return $this;
	}

	/**
	 * @return string
	 */
	public function toString(): string {
		return $this->buffer;
	}

	/** @return string */
	public function __toString(): string {
		return $this->toString();
	}
} 