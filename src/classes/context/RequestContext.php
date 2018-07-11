<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:29
 */

namespace classes\context;


class RequestContext {
	/** @var RequestMethod */
	private $requestMethod;

	/** RequestContext constructor. */
	public function __construct() {
		$this->requestMethod = new RequestMethod();
	}

	public function getContentType() { return $_SERVER["CONTENT_TYPE"]; }
	public function compareContentType($contentType) { return trim(strtolower($_SERVER["CONTENT_TYPE"])) == trim(strtolower($contentType)); }

	public function getUserAgent() { return $_SERVER["HTTP_USER_AGENT"]; }

	public function getRemoteHost() { return $_SERVER["REMOTE_HOST"]; }

	public function getRemoteAddr() { return $_SERVER["REMOTE_ADDR"]; }

	public function cookie($key, $defaultValue = null, $trim = false) {
		if (!is_scalar($key)) throw new \InvalidArgumentException("invalid key type");

		if ($trim) return array_key_exists($key, $_COOKIE) ? $this->requestParamTrim($_COOKIE [$key], $defaultValue) : $defaultValue;
		else return array_key_exists($key, $_COOKIE) ? $_COOKIE [$key] : $defaultValue;
	}

	public function rawData() {
		static $data = null;

		// FIXME enctype=multipart/form-data 를 지원하지 않는다. 예외 처리 해주어야 한다.
		if ($data === null) {
			$data = file_get_contents('php://input'); // FIXME MAGIC_QUEOTE 에 대한 대비책이 필요 하다.
		}

		return $data;
	}

	public function getRequestMethod() { return $this->requestMethod; }

	public function postParam($key, $defaultValue = null, $trim = false) {
		if(!$this->requestMethod->isPost())
			throw new \RuntimeException("request method is not post");

		if (!is_scalar($key)) throw new \InvalidArgumentException("invalid key type");

		$vars = getApplicationContext()->isDebug() && getApplicationContext()->getDebugFlag('requestPostFromRequest') != 0
			? $_REQUEST
			: $_POST;

		if ($trim) return array_key_exists($key, $vars) ? $this->requestParamTrim($vars [$key], $defaultValue) : $defaultValue;
		else return array_key_exists($key, $vars) ? $vars [$key] : $defaultValue;
	}

	public function getParam($key, $defaultValue = null, $trim = false) {
		if (!is_scalar($key)) throw new \InvalidArgumentException("invalid key type");

		if ($trim) return array_key_exists($key, $_GET) ? $this->requestParamTrim($_GET [$key], $defaultValue) : $defaultValue;
		else return array_key_exists($key, $_GET) ? $_GET [$key] : $defaultValue;
	}

	private function requestParamTrim($value, $defaultValue) {
		if (is_array($value)) {
			if (count($value) == 0) return null;

			foreach ($value as &$v) {
				if (strlen(trim($v)) == 0) {
					$v = $defaultValue;
				}
			}

			return $value;
		} else {
			if (strlen(trim($value)) == 0) return $defaultValue;
			else return $value;
		}
	}

}