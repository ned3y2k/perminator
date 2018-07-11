<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 13
 * 시간: 오후 8:53
 */
namespace classes\database\query\mapper\node;


class QueryTextNode extends AbstractChildNode {
	private $text;

	public function setText($text) { $this->text = $text; }

	public function __toString() {
		$buff = $this->text;
		foreach($this->childNodes as $childNode) {
			$tmp = $childNode->__toString();

			if(strlen($tmp) > 0)
				$buff .= $tmp;
		}

		return $buff;
	}
}