<?php
namespace classes\web\script\http;
/**
 * @author ned3y2k
 * Perminator WebRequest Boxing Class
 */
class Request { // FIXME DataBinder와 연결 하는 작업 필요
	private static $requestInstance = array();
	private $parameters = array();
	const GET = 0;
	const POST = 1;
	const COOKIE = 2;
	const SESSION = 3;

	/**
	 *
	 * @param string $method
	 * @return Request
	 */
	public static function getInstance($methodType) {
		if (!isset(self::$requestInstance[$methodType]))
			self::$requestInstance[$methodType] = new Request($methodType);
		return self::$requestInstance[$methodType];
	}

	/**
	 * new Request($_GET)
	 *
	 * @param string[] $method
	 */
	private function __construct($methodType) {
		$method = null;
		switch ($methodType) {
		case Request::GET:
			$method = $_GET;
			break;
		case Request::POST:
			$method = $_POST;
			break;
		case Request::COOKIE:
			$method = $_COOKIE;
			break;
		case Request::SESSION:
			$method = $_SESSION;
			break;
		}
		$this->processRequest($method);

	}
	/**
	 * @param method
	 */private function processRequest($method) {
		if (get_magic_quotes_gpc()) {
			foreach ($method as $key => $value) {
				$this->parameters[$key] = stripslashes($value);
			}
		} else {
			$this->parameters = $method;
		}
	}

	public function getQueryString(array $excludeKeys = null) {
		$query = "";

		if(is_null($excludeKeys)) $excludeKeys = array();
		$excludeKeys = array_merge(array('class', 'method'), $excludeKeys);
		foreach ($this->parameters as $key => $value) {
			if(!in_array($key, $excludeKeys))
				$query .= "&{$key}={$value}";
		}

		return substr($query, 1);
	}

	public function print_r() {
		print_r($this->parameters);
	}

	/**
	 * 웹 파라미터를 가져온다.
	 * 없다면 기본값을 반환
	 *
	 * @param String $key
	 *        	키
	 * @param String $defaultValue
	 *        	기본값[optional]
	 * @return mixed
	 */
	public function getParameter($key, $defaultValue = NULL) {
		return array_key_exists($key, $this->parameters)
				&& $this->parameters[$key] != NULL ? $this->parameters[$key]
				: $defaultValue;
	}

	/**
	 * 가지고 있는 모든 파라미터를 반환
	 * @return multitype:
	 */
	public function getParameters() {
		return $this->parameters;
	}
}
