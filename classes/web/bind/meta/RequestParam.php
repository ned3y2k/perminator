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
	private $incomplete;

	/**
	 * @var mixed
	 */
	public $value;

	/**
	 * @param string $className
	 * @param string $method RequestParam::const
	 * @param boolean $required
	 * @param boolean $incomplete
	 * @throws \InvalidArgumentException
	 */
	public function __construct($className, $method, $required = false, $incomplete = false) {
		$this->validateArgument ( $className, $method, $required );

		$this->className = $className;
		$this->method = $method;
		$this->required = $required;
		$this->incomplete = $incomplete;
	}

	/**
	 *
	 * @return string
	 */
	public function getClassName() {
		return $this->className;
	}

	/**
	 * return to http method
	 *
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 *
	 * @return boolean
	 */
	public function isRequired() {
		return $this->required;
	}

	/**
	 * @return boolean
	 */
	public function isIncomplete() {
		return $this->incomplete;
	}

	/**
	 *
	 * @param string $className
	 * @param string $method RequestParam::const
	 * @param boolean $required
	 * @throws \InvalidArgumentException
	 */
	private function validateArgument($className, $method, $required) {
		if (! is_string ( $className ))
			throw new \InvalidArgumentException ();
		if (! is_bool ( $required ))
			throw new \InvalidArgumentException ();
		if ($method != self::METHOD_GET && $method != self::METHOD_POST)
			throw new \InvalidArgumentException ();
	}
}