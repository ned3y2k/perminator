<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-01
 * 시간: 오후 11:42
 */

namespace classes\lang;


class UniqueObject implements IUniqueObject {
	private $objectHashCode;

	/** @return string */
	function hashCode() {
		if(!isset($this->objectHashCode))
			$this->objectHashCode = $this->createId();

		return $this->objectHashCode;
	}

	function __clone() {
		$this->objectHashCode = $this->createId();
	}

	/** @return string */
	private function createId() {
		return hash('md5', uniqid(get_class($this)).rand(0,255));
	}
}