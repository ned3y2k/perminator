<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:29
 */

namespace classes\context;

use classes\lang\ArrayUtil;
use classes\web\MultiPartFile;

class RequestContext
{
	use ContextAssistTrait;
	use RequestMethod;

	/** @var RequestSession */
	private $requestSession;
	/** @var bool */
	private $multiPart;
	/** @var \classes\web\MultiPartFile[]|\classes\web\MultiPartFile[][] FIXME MultiFileGroup? */
	private $multiPartFiles;

	public function __construct() {
		$this->multiPart = strpos(ArrayUtil::getValue($_SERVER, 'CONTENT_TYPE', ''), 'multipart/form-data') !== false;

		if($this->isPost() && $this->multiPart){
			$this->multiPartFiles = $this->resolveMultiPartFiles();
		}

	}

	private function resolveMultiPartFiles() {
		if (isset($_FILES) && is_array($_FILES) && count($_FILES) != 0) {
			$items = array();

			foreach ($_FILES as $key => $FILE) {
				if(!is_array($FILE[ 'tmp_name' ]))
					$items[ $key ] = new MultiPartFile($FILE[ 'tmp_name' ], $FILE[ 'name' ], $FILE[ 'size' ], $FILE[ 'error' ], $type = $FILE[ 'type' ]);
				elseif(is_array($FILE))
					$items[ $key ] = $this->resolveMultiPartFileArray($FILE);
				else
					throw new \UnsupportedOperationException();
			}

			return $items;
		} else {
			return null;
		}
	}

	private function resolveMultiPartFileArray(array $FILE) {
		$files = array();
		$keys = array_keys($FILE[ 'tmp_name' ]);
		foreach($keys as $key) {
			$files[] = new MultiPartFile($FILE[ 'tmp_name' ][$key], $FILE[ 'name' ][$key], $FILE[ 'size' ][$key], $FILE[ 'error' ][$key], $type = $FILE[ 'type' ][$key]);
		}

		return $files;
	}


	public function getUserAgent(): string {
		if (TEST)
			return 'TestUnit';
		else
			return $_SERVER['HTTP_USER_AGENT'];
	}

	/**
	 * @FIXME ApplicationContext 로 올라가야 하는 개념이다
	 * @return RequestSession
	 */
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

		if($this->multiPart)
			throw new \InvalidArgumentException("multipart form data not supported");

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

	public function multiPartFiles() {
		return $this->multiPartFiles;
	}

	/**
	 * @param $name
	 *
	 * @return MultiPartFile|null
	 */
	public function multiPartFile($name) {
		if($this->multiPartFiles && array_key_exists($name, $this->multiPartFiles) ) {
			return $this->multiPartFiles[$name];
		}
		return null;
	}
}