<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-30
 * Time: 오전 7:34
 */

namespace classes\model;

use classes\runtime\serialization\json\JsonSerializable;

class Hexadecimal implements JsonSerializable {
	private $data;
	private $unsigned = false;

	public static function fromString($string): Hexadecimal {
		$r       = new Hexadecimal();
		$r->data = pack("H*", $string);

		return $r;
	}

	public function isUnsigned(): bool {
		return $this->unsigned;
	}

	public function setUnsigned($unsigned) {
		$this->unsigned = $unsigned;
	}

	/**
	 * @param \stdClass $out
	 *
	 * @return void
	 */
	public function getSerializeConfig(\stdClass &$out) {
		throw new \UnsupportedOperationException("Hexadecimal does not support json serialize");
	}

	public function getBytes() {
		$data = $this->unsigned ? unpack("C*", $this->data) : unpack("c*", $this->data);
		return array_values($data);
	}

	public function toString() {
		return implode(unpack('H*', $this->data));
	}
}