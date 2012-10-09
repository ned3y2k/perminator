<?php
namespace classes\web\bind\meta;

final class RequestParam {
	const METHOD_GET = 0;
	const METHOD_POST = 1;
	const METHOD_COOKIE = 2;
	const METHOD_SESSION = 3;

	private $className;
	private $method;
	private $required;

	public $value;

	public function __construct($className, $method, $required) {
		$this->validateArgument($className, $method, $required);

		$this->className = $className;
		$this->method = $method;
		$this->required = $required;
	}

	/**
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}

	/**
	 * return to http method
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * @return boolean
	 */
	public function getRequired() {
		return $this->required;
	}

	/**
	 * @param className
	 * @param method
	 * @param required
	 */
	 private function validateArgument($className, $method, $required) {
		if (!is_string($className))
			throw new \InvalidArgumentException();
		if (!is_bool($required))
			throw new \InvalidArgumentException();
		if ($method != self::METHOD_GET && $method != self::METHOD_POST)
			throw new \InvalidArgumentException();
	}

}

?>