<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-14
 * 시간: 오후 3:01
 */
namespace classes\web\response;

/**
 * Class ImageResult
 *
 */
class StringResultResponse implements IResponseDelegate {
	/** @var string */
	private $contentType;
	/** @var string */
	private $content;
	private $compress = true;

	/**
	 * @param string $content
	 * @param string $contentType text/html
	 */
	public function __construct($content, $contentType) {
		$this->contentType = $contentType;
		$this->content = $content;
	}

	/**
	 * @param bool $compress
	 * @return StringResultResponse
	 */
	public function setCompress(bool $compress) {
		$this->compress = $compress;
		return $this;
	}

	/** @return string */
	public function getContentType() { return $this->contentType; }

	/** @return string */
	public function getContent() { return $this->content; }

	public function createResponse(): HttpResponse {
		$res = new HttpResponse();

		$res->getSetting()->compress = true;

		$res->setContentType($this->getContentType());
		$res->setBody($this->getContent());

		return $res;
	}

	public static function createHtmlPage(string $html, bool $compress = true) {
		$res = new HttpResponse();

		$res->getSetting()->compress = $compress;

		$res->setContentType('text/html');
		$res->setBody($html);

		return $res;
	}
}