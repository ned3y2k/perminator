<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 8. 18
 * 시간: 오후 12:37
 */

namespace classes\model\html;


class StyleElement extends HeadElement {
	/** @var string */
	private $body;

	public function onCreate() { }

	/** @return string */
	public function getTagName() { return 'style'; }

	public function appendChild(HTMLElement $element) { throw new \UnsupportedOperationException(); }

	/** @param string $body */
	public function setBody($body) { $this->body = $body; }

	/** @return string */
	public function toString() {
		$strBuilder = HTMLContext::createStringBuilder();
		$strBuilder->append('<style');
		$strBuilder->append($this->attrsToString());
		$strBuilder->append('>');

		$strBuilder->append($this->body);

		$strBuilder->append('</style>');

		return $strBuilder->toString();
	}
}