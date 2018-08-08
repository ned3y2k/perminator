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
 * @package classes\common\api\model
 */
class ImageUrlResult implements IResponseDelegate {
	private static $contentContentTypeMap = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png');
	/** @var string */
	private $contentType;
	/** @var string */
	private $url;

	public function __construct($url) {
		$buff = explode('?', $url);
		$extName = pathinfo($buff[0], PATHINFO_EXTENSION);
		$this->contentType = self::$contentContentTypeMap[$extName];
		if ($this->contentType == null)
			throw new \UnsupportedOperationException('unsupported type');

		$this->url = $url;
	}

	/** @return string */
	public function getContentType() { return $this->contentType; }

	/** @return string */
	public function getUrl() { return $this->url; }

	function createResponse(): HttpResponse {
		$res = new HttpResponse();
		$res->getSetting()->compress = true;
		$res->setContentType($this->getContentType());
		$res->redirect($this->getUrl());

		return $res;
	}
}