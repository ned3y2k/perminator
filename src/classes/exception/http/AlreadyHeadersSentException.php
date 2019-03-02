<?php
/**
 * User: Kyeongdae
 * Date: 2018-08-01
 * Time: 오전 2:22
 */

namespace classes\exception\http;


class AlreadyHeadersSentException extends HTTPResponseException {

	public function __construct($string, $replace, $http_response_code) {
		parent::__construct(sprintf('header: %s, replace: %s, response_code', $string, $replace, $http_response_code), 500);
	}
}