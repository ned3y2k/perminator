<?php
load_lib('func/request');


/**
 * 캐쉬의 유효 기간을 바로 만료시킴
 */
function response_expire_now() {
	getApplicationContext()->getResponseContext()->putRawHeader("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
	getApplicationContext()->getResponseContext()->putRawHeader("Pragma: no-cache"); //HTTP 1.0
	getApplicationContext()->getResponseContext()->putRawHeader("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}


/**
 * 해당 페이지를 GET 으로 재요청
 * @param bool $exit
 * @param bool $permanently
 */
function response_reload($exit = false, $permanently = false) {
	if ($permanently) getApplicationContext()->getResponseContext()->putRawHeader("HTTP/1.1 301 Moved Permanently");

	if (request_method_is_post() && strpos($_SERVER["HTTP_REFERER"], $_SERVER["REQUEST_URI"]) > -1) {
		getApplicationContext()->getResponseContext()->putRawHeader('location: ' . $_SERVER["HTTP_REFERER"]);
	} else {
		getApplicationContext()->getResponseContext()->putRawHeader('location: ' . $_SERVER["REQUEST_URI"]);
	}
}