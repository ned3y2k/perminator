<?php

namespace classes\lang;

/**
 * @author 경대
 * Perminator Class
 */
class FileStringBuilder {
	private $buffer = array ();

	private $tmpFileName;
	public function __construct() {
		$this->tmpFileName = _DIR_VAR_.'tmp'.DIRECTORY_SEPARATOR.uniqid();
	}

	function __destruct() {
		unset($this->tmpFileName);
	}

	/**
	 * @param string $str
	 */
	public function append($str) {
		if(!is_string($str) && $str instanceof self) throw new \InvalidArgumentException();
		file_put_contents($this->tmpFileName, $str, FILE_APPEND);
	}

	/**
	 * @return string
	 */
	public function toString() {
		return file_get_contents($this->tmpFileName);
	}

	public function __toString() {
		return $this->toString();
	}
}