<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 4:42
 */

namespace classes\api\model;


class ImageUrlResult {
	private static $contentContentTypeMap = array('jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'png' => 'image/png');
	/** @var string */
	private $contentType;
	/** @var string */
	private $url;

	public function __construct($url) {
		$buff = explode('?', $url);
		$extName = pathinfo($buff[0], PATHINFO_EXTENSION);
		$this->contentType = @self::$contentContentTypeMap[$extName];
		if($this->contentType == null)
			throw new \UnsupportedOperationException('unsupported type');

		$this->url = $url;
	}

	/** @return string */
	public function getContentType() { return $this->contentType; }

	/** @return string */
	public function getUrl() { return $this->url; }
}