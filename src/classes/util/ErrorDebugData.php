<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-17
 * Time: 오전 8:48
 */

namespace classes\util;


class ErrorDebugData {
	/** @var string */
	private $requestUri;
	/** @var array */
	private $sessionVars;
	/** @var array */
	private $postVars;
	/** @var array */
	private $getVars;
	/** @var array */
	private $cookieVars;
	/** @var string */
	private $requestContentType;

	/** 접근 불가 build 접근 */
	private function __construct() { }

	/** @return ErrorDebugData */
	static function build() {
		$instance = new self();

		$instance->requestContentType = $_SERVER["CONTENT_TYPE"];
		$instance->requestUri = $_SERVER['REQUEST_URI'];
		$instance->sessionVars = $_SESSION;
		$instance->cookieVars = $_COOKIE;
		$instance->postVars = $_POST;
		$instance->getVars = $_GET;

		return $instance;
	}
}
