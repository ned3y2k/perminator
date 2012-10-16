<?php

namespace classes\lang;

/**
 * TODO 참고 http://docs.oracle.com/javase/1.5.0/docs/api/java/lang/StringBuilder.html
 * @author User
 *
 */
class StringBuilder {
	private $buffer = array ();

	/**
	 * @param string $str
	 */
	public function append($str) {
		$this->buffer [] = $str;
	}

	/**
	 * @return string
	 */
	public function toString() {
		return implode ( $this->buffer );
	}
}

?>