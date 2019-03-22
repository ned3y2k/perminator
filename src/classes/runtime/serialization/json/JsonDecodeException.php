<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-17
 * Time: 오전 10:30
 */

namespace classes\runtime\serialization\json;


class JsonDecodeException extends \InvalidArgumentException {
	private $jsonString;

	public function __construct($code, $jsonString) {
		parent::__construct($this->codeToString($code), $code);
		$this->jsonString = $jsonString;
	}

	public function getJsonString(): string {
		return $this->jsonString;
	}

	private function codeToString($code) {
		switch ($code) {
			case JSON_ERROR_DEPTH:
				return 'JSON_ERROR_DEPTH';
			case JSON_ERROR_CTRL_CHAR:
				return 'JSON_ERROR_CTRL_CHAR';
			case JSON_ERROR_SYNTAX;
				return 'JSON_ERROR_SYNTAX';
			default:
				throw new \InvalidArgumentException('invalid json error code');
		}
	}
}