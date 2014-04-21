<?php
namespace classes\filter;

interface IFilter {
	/**
	 * @param mixed $value
	 * @return mixed
	 */
	function doFilter($value);
}

class SimpleNonBlankFilter implements IFilter {
	private $errMsg;
	private $exceptionClassName;

	public function __construct($errMsg = 'blank field', $exceptionClassName = '\InvalidArgumentException') {
		$this->errMsg = $errMsg;
		$this->exceptionClassName = $exceptionClassName;
	}

	public function doFilter($value) {
		$exceptionClassName = $this->exceptionClassName;

		if(strlen($value) == 0 || is_null($value)) throw new $exceptionClassName($this->errMsg);
		return $value;
	}
}

class TrimFilter implements IFilter {
	private $default;

	public function __construct($default = null) {
		$this->default = $default;
	}

	public function doFilter($value) {
		return strlen($value) == 0 ? $this->default : $value;
	}
}