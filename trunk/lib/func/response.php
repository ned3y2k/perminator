<?php
use classes\model\html\JavaScriptElement;
load_lib('func/request');

/**
 * 페이지 리다이렉트
 * @param stgring $location
 */
function response_redirect($location) {
	if (headers_sent ()) echo "<script type='text/javascript'>location.href='{$location}'</script>";
	else header ( "location: {$location}" );
}


/**
 * 경고 메시지 출력뒤 페이지 이동
 * @param unknown $msg
 * @param string $location
 */
function response_alert_redirect($msg, $location = null) {
	header ( "Content-Type: text/html; charset=UTF-8" );
	$content = "";

	$script = new JavaScriptElement ();
	$content = "alert('" . addslashes ( $msg ) . "');";

	if (! is_null ( $location )) $content .= "location.href = '" . addslashes ( $location ) . "';";

	$script->setContent($content);
	echo $script;
	exit;
}

/**
 * 캐쉬의 유효 기간을 바로 만료시킴
 */
function response_expire_now() {
	header("Cache-Control: no-cache, must-revalidate"); //HTTP 1.1
	header("Pragma: no-cache"); //HTTP 1.0
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}


/**
 * 해당 페이지를 GET으로 재요청
 */
function response_reload() {
	if(request_method_is_post() && strpos($_SERVER["HTTP_REFERER"], $_SERVER["REQUEST_URI"]) > -1) {
		header('location: '.$_SERVER["HTTP_REFERER"]);
	} else {
		header('location: '.$_SERVER["REQUEST_URI"]);
	}
}