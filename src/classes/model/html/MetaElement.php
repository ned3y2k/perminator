<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 8. 18
 * 시간: 오후 12:37
 */

namespace classes\model\html;


class MetaElement extends HeadElement {

	public function onCreate() { }

	public function getTagName() { return 'meta'; }

	public function toString() {
		$strBuilder = HTMLContext::createStringBuilder();
		$strBuilder->append('<meta');
		$strBuilder->append($this->attrsToString());

		if ($this->htmlContext->isXHTML) {
			$strBuilder->append(' />');
		} else {
			$strBuilder->append('>');
		}

		return $strBuilder->toString();
	}
}