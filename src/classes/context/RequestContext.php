<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:29
 */

namespace classes\context;

use classes\lang\ArrayUtil;

class RequestContext
{
	use ContextAssistTrait;
	use RequestMethod;

	/** @var RequestSession */
	private $requestSession;


	public function getUserAgent(): string {
		if (TEST)
			return 'TestUnit';
		else
			return $_SERVER['HTTP_USER_AGENT'];
	}

	public function getSession() {
		if (!$this->requestSession)
			$this->requestSession = new RequestSession($this);

		return $this->requestSession;
	}

	/**
	 * 연결 스키마를 돌려준다.
	 * @link http://php.net/manual/en/function.http-build-url.php#114753
	 * @return string http https
	 */
	public function getScheme(): string { return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; }

	public function getContentType() { return $_SERVER["CONTENT_TYPE"]; }

	public function compareContentType($contentType) { return trim(strtolower($_SERVER["CONTENT_TYPE"])) == trim(strtolower($contentType)); }

	public function getRemoteHost() { return $_SERVER["REMOTE_HOST"]; }

	public function getRemoteAddr() { return array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER["REMOTE_ADDR"]; }

	public function cookie($key, $defaultValue = null, $trim = false) {
		if (!is_scalar($key)) throw new \InvalidArgumentException("invalid key type");

		if ($trim) return array_key_exists($key, $_COOKIE) ? $this->requestParamTrim($_COOKIE [$key], $defaultValue) : $defaultValue;
		else return array_key_exists($key, $_COOKIE) ? $_COOKIE [$key] : $defaultValue;
	}


	/**
	 * request payload 에서 값을 돌려줌
	 *
	 * @param string $key
	 * @param string $defaultValue
	 * @param bool   $trim
	 *
	 * @return mixed
	 */
	function requestPayload($key, $defaultValue = null, $trim = false) {
		$rawDataArray = $this->requestPayloadArray();

		if ($trim) return array_key_exists($key, $rawDataArray) ? request_user_var_trim($rawDataArray [ $key ], $defaultValue) : $defaultValue;
		else return array_key_exists($key, $rawDataArray) ? $rawDataArray [ $key ] : $defaultValue;
	}

	/**
	 * php://input(request payload) 에 전체 내용을  array 로 돌려줌
	 * @return array
	 */
	function requestPayloadArray() {
		static $data = null;

		if ($data === null) {
			$data = array();
			parse_str($this->rawRequestPayload(), $data);
		}

		return $data;
	}

	public function rawRequestPayload() {
		static $data = null;

		// FIXME enctype=multipart/form-data 를 지원하지 않는다. 예외 처리 해주어야 한다.
		if ($data === null) {
			$data = file_get_contents('php://input'); // FIXME MAGIC_QUEOTE 에 대한 대비책이 필요 하다.
		}

		return $data;
	}


	public function postParam($key, $defaultValue = null, $trim = false) {
		if (!$this->isPost())
			throw new \RuntimeException("request method is not post");

		if (!is_scalar($key)) throw new \InvalidArgumentException("invalid key type");

		$debugContext = getApplicationContext()->getDebugContext();
		$vars = $debugContext->available() && $debugContext->get('requestPostFromRequest') != 0
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


	/**
	 * 해당 인코딩(charset이 아닌 압축)을 지원하는지 여부<br>
	 * request_accept_encoding('gzip')
	 * @param $encoding
	 *
	 * @return bool
	 */
	public function hasAcceptEncoding($encoding) {
		return stripos(ArrayUtil::getValue($_SERVER, 'HTTP_ACCEPT_ENCODING'), $encoding) !== false;
	}
}