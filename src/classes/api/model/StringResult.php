<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 4:42
 */

namespace classes\api\model;


class StringResult {
	/** @var string */
	private $contentType;
	/** @var string */
	private $content;

	/**
	 * @param string $content
	 * @param string $contentType text/html
	 */
	public function __construct($content, $contentType) {
		$this->contentType = $contentType;
		$this->content = $content;
	}

	/** @return string */
	public function getContentType() { return $this->contentType; }

	/** @return string */
	public function getContent() { return $this->content; }
}