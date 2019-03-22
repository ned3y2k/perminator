<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-10-16
 * 시간: 오전 10:23
 */
namespace classes\web\mvc;

class StringSubPage extends SubPage {
	/** @var string */
	private $value;

	public function __construct($value) {
		$this->value = $value;
	}

	public function __toString() { return $this->value; }
}