<?php

namespace classes\lang;

/**
 * @author 경대
 * Perminator Class
 */
class StringBuilder {
	private $buffer = array ();

	/**
	 * @param string $str
	 */
	public function append($str) {
		if(!is_string($str) && $str instanceof self) throw new \InvalidArgumentException();
		$this->buffer [] = $str;
	}

	/**
	 * @return string
	 */
	public function toString() {
		return implode ( $this->buffer );
	}

	public function __toString() {
		return $this->toString();
	}
}