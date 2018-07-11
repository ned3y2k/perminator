<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 12
 * 시간: 오전 9:39
 */
namespace classes\web;

use classes\lang\ArrayUtil;
use classes\lang\ObjectUtil;

if(!defined('HTTP_REDIRECT_PERM')) {
	/**
	 * guess applicable redirect method
	 * @link http://php.net/manual/en/http.constants.php
	 */
	define ('HTTP_REDIRECT', 0);

	/**
	 * permanent redirect (301 Moved permanently)
	 * @link http://php.net/manual/en/http.constants.php
	 */
	define ('HTTP_REDIRECT_PERM', 301);

	/**
	 * standard redirect (302 Found)
	 * RFC 1945 and RFC 2068 specify that the client is not allowed
	 * to change the method on the redirected request. However, most
	 * existing user agent implementations treat 302 as if it were a 303
	 * response, performing a GET on the Location field-value regardless
	 * of the original request method. The status codes 303 and 307 have
	 * been added for servers that wish to make unambiguously clear which
	 * kind of reaction is expected of the client.
	 * @link http://php.net/manual/en/http.constants.php
	 */
	define ('HTTP_REDIRECT_FOUND', 302);

	/**
	 * redirect applicable to POST requests (303 See other)
	 * @link http://php.net/manual/en/http.constants.php
	 */
	define ('HTTP_REDIRECT_POST', 303);

	/**
	 * proxy redirect (305 Use proxy)
	 * @link http://php.net/manual/en/http.constants.php
	 */
	define ('HTTP_REDIRECT_PROXY', 305);

	/**
	 * temporary redirect (307 Temporary Redirect)
	 * @link http://php.net/manual/en/http.constants.php
	 */
	define ('HTTP_REDIRECT_TEMP', 307);
}

/**
 * Class HttpResponse
 * @package classes\web
 * @link http://php.net/manual/en/class.httpresponse.php
 */
class HttpResponse {
	/** @var string[] */
	private $headers = array();
	/** @var bool */
	private $requireSession;
	/** @var string */
	private $body;
	/** @return HttpResponseSetting */
	private $setting;
	/** @var int */
	private $status;

	/** @param bool $requireSession 세션 시작이 필요한 경우에 사용 */
	function __construct($requireSession = false) {
		if (version_compare(phpversion(), '5.4.0', '>=') && session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		} elseif(!isset($_SESSION)) {
			session_start();
		}

		$this->requireSession = $requireSession;
		$this->setting = new HttpResponseSetting();
	}

	/** @return HttpResponseSetting */
	public function getSetting() { return $this->setting; }

	/**
	 * 헤더 값을 넣는다.
	 * @param string $name
	 * @param string $value
	 */
	public function putHeader($name, $value) {
		if(strtolower($name) == 'content-type') $this->setContentType($value);
		elseif(strtolower($name) == 'etag') $this->setETag($value);
		elseif(strtolower($name) == 'last-modified') $this->setLastModified($value);

		else $this->headers[$name] = $value;
	}

	/**
	 * 쿠키 값읗 넣음
	 * @param string $name
	 * @param string $value
	 */
	public function putCookie($name, $value) { setcookie($name, $value); }

	/**
	 * 세션 값읗 넣음
	 * @param string $name
	 * @param string $value
	 */
	public function putSession($name, $value) {
		if(!$this->requireSession) throw new \BadMethodCallException("requireSession is false. requireSession set by the constructor to true");

		$_SESSION[$name] = $value;
	}

	/**
	 * 결과 넣음
	 * @param string|\stdClass $body
	 * @throws \InvalidArgumentException
	 */
	public function setBody($body) {
		if(!is_scalar($body) && !ObjectUtil::hasToString($body)) {
			$stack = ArrayUtil::getValue(debug_backtrace(), 0);

			throw new \InvalidArgumentException("can't convert to string.".' '.$stack['file'].' on line ' . $stack['line']);
		}
		$this->body = $body;
	}

	/** @param bool $clean_ob 이전 내용을 전부 지우며 자신의 버퍼를 비운다. */
	public function send($clean_ob = true) {
		if($clean_ob) {
			if(!TEST && ob_get_length() != 0) {
				ob_end_flush();
			}
		}

		if(!empty($this->status)) {
			http_response_code($this->status);
		}

		foreach($this->headers as $name => $val) {
			getApplicationContext()->getResponseContext()->putRawHeader(sprintf("%s: %s", $name, $val));
		}


		$body = is_object($this->body) ? $this->body->__toString() : $this->body;
		if($body !== null && strlen($body) > 0) {
			if(ini_get('output_compression') !== '1' && ini_get('zlib.output_compression') !== 1 && $this->setting->compress == true && $this->isSupportedCompress()) {
				getApplicationContext()->getResponseContext()->setContentEncoding('gzip');
				echo gzencode($body);
			} else {
				echo $body;
			}

			if($clean_ob)
				$this->body = null;
		}
	}

	/**
	 * 클라이언트가 압축된 콘텐트 해석을 지원하는지 여부
	 * @return bool
	 */
	private function isSupportedCompress() {
		load_lib('func/request');
		return request_accept_encoding('gzip');
	}

	/** @param $etag */
	public function setETag ($etag) {
		$this->headers['ETag'] = $etag;
	}

	/**
	 * @return string
	 */
	public function getETag() {
		return $this->getHeader('ETag');
	}

	/** @return string[] */
	public function getHeaders () { return $this->headers; }

	/**
	 * @param $name
	 * @param $value
	 * @param bool $replace
	 * @return bool
	 */
	public function setHeader ( $name, $value, $replace = true ) { throwNewUnimplementedException(); }

	/**
	 * @param string $name
	 * @return mixed
	 */
	public function getHeader ($name) {
		$val = ArrayUtil::getValue($this->headers, $name);
		if($val === null) {
			foreach(array_keys($this->headers) as $key) {
				if(strtolower($key) == strtolower($name)) {
					return $this->headers[$key];
				}
			}
		}
		return $val;
	}

	/**
	 * @param string $file
	 * @return bool
	 */
	public function setFile($file) { throwNewUnimplementedException();  return false; }
	/** @return string */
	public function getFile() { throwNewUnimplementedException(); }

	/**
	 * @param mixed $data
	 * @return bool
	 */
	public function setData ( $data ) { throwNewUnimplementedException(); return false; }
	/** @return string */
	public function getData() { throwNewUnimplementedException(); }

	/** @param string $contentType */
	public function setContentType($contentType) { $this->headers['Content-type'] = $contentType; }
	/** @return string */
	public function getContentType() { return $this->getHeader('Content-type'); }

	/**
	 * @param string $fileName
	 * @param bool $inline
	 */
	public function setContentDisposition($fileName, $inline = false) { throwNewUnimplementedException(); }
	/** @return string */
	public function getContentDisposition () { throwNewUnimplementedException(); }

	/**
	 * @param string $control
	 * @param int $maxAge
	 * @param bool $mustRevalidate
	 */
	public function setCacheControl ($control, $maxAge = 0, $mustRevalidate = true) { throwNewUnimplementedException(); }
	/** @return string */
	public function getCacheControl() { throwNewUnimplementedException(); return ""; }

	/**
	 * @param bool $gzip
	 * @return bool
	 */
	public function setGzip($gzip) { throwNewUnimplementedException(); }

	/** @param bool $cache */
	public function setCache($cache) { throwNewUnimplementedException(); }
	/** @return bool */
	public function getCache() { throwNewUnimplementedException(); }

	public function capture() { throwNewUnimplementedException(); }

	/** @return string */
	public function getRequestBody() { return $this->body; }
	/** @return resource */
	public function getRequestBodyStream() { throwNewUnimplementedException(); }

	/** @param int $bytes */
	public function setBufferSize($bytes) { throwNewUnimplementedException(); }
	/** @return int */
	public function getBufferSize() { throwNewUnimplementedException(); }

	/**
	 * @param resource $stream
	 * @return bool
	 */
	public function setStream($stream) { throwNewUnimplementedException(); }
	/** @return resource */
	public function getStream () { throwNewUnimplementedException(); }

	/**
	 * @param float $second
	 * @return bool
	 */
	public function setThrottleDelay($second) { throwNewUnimplementedException(); }
	/** @return float */
	public function getThrottleDelay () { throwNewUnimplementedException(); }

	/**
	 * @param string $magicFile
	 * @param int $magicMode
	 */
	public function guessContentType ( $magicFile, $magicMode = 4 ) { throwNewUnimplementedException(); }



	/**
	 * @param $url
	 * @param array $params associative array of query parameters
	 * @param bool $session whether to append session information
	 * @param int $status custom response status code
	 */
	public function redirect ($url, array $params = null, $session = false, $status = 301) {
		$path = $url;

		$newParams = array();

		if($params !== null && is_array($params))
			$newParams = array_merge($newParams, $params);

		if($session == true)
			$newParams[session_name()] = session_id();

		http_response_code($status);
		if(count($newParams) != 0) {
			$path .= '?'.http_build_query($newParams);
		}
		
		$this->headers['location'] = $path;
	}


	/**
	 * @param int $timeStamp
	 * @return bool
	 */
	public function setLastModified($timeStamp) { $this->headers['Last-Modified'] = date('D, d M Y H:i:s T', $timeStamp); }

	/** @return int */
	public function getLastModified () { return $this->getHeader('Last-Modified'); }

	/**
	 * @param int $status
	 * @return bool
	 */
	public function status($status) { $this->status = $status; }
}