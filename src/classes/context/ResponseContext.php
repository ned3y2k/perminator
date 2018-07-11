<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:28
 */

namespace classes\context;


class ResponseContext {
	private $contextType     = "text/html";
	private $charset         = "utf-8";
	private $contentEncoding = '';

	/** @return string */
	public function getContextType(): string { return $this->contextType; }

	/** @return string */
	public function getCharset(): string { return $this->charset; }

	/** @return string */
	public function getContentEncoding(): string { return $this->contentEncoding; }

	/**
	 * @param string $string
	 * @param bool   $replace
	 * @param null   $http_response_code
	 *
	 * @return $this
	 */
	public function putRawHeader(string $string, bool $replace = true, $http_response_code = null) {
		if (!TEST) {
			header($string, $replace, $http_response_code);
		} else {
			printf('%s %s %s\n', $string, $replace ? 'true' : 'false', $http_response_code ?? 'null');
		}

		return $this;
	}

	public function headerRemove(string $name = null) {
		header_remove($name);

		return $this;
	}

	/**
	 * @param string $contextType
	 * @param string $charset            'utf-8'
	 * @param string $contentDisposition = 'attachment'
	 * @param string $filename
	 *
	 * @return ResponseContext
	 */
	public function setContextType(string $contextType, string $charset = 'utf-8', string $contentDisposition = null, string $filename = null): ResponseContext {
		$this->contextType = $contextType;
		$header            = 'Content-Type: ' . $contextType;
		if ($charset)
			$header .= '; charset=' . $charset;
		if ($contentDisposition)
			$header .= '; Content-Disposition=' . $contentDisposition;
		if ($contentDisposition)
			$header .= '; filename=' . $filename;

		$this->putRawHeader($header);

		return $this;
	}


	/**
	 * @param string $charset
	 *
	 * @return ResponseContext
	 */
	public function setCharset(string $charset): ResponseContext {
		$this->charset = $charset;

		return $this;
	}

	/**
	 * @param string $contentEncoding
	 *
	 * @return ResponseContext
	 */
	public function setContentEncoding(string $contentEncoding): ResponseContext {
		$this->contentEncoding = $contentEncoding;
		$this->putRawHeader('Content-Encoding: ' . $contentEncoding);

		return $this;
	}

	public function getAllHeader() {
		return getallheaders();
	}
}