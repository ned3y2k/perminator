<?php
/**
 * 셔션 ID 가져 오기를 시도
 * @param Closure $sessionStarter
 * @throws RuntimeException
 * @return string
 */
function request_session_id(Closure $sessionStarter = null) {
	if (session_id () == '') throw new RuntimeException ( 'you must be sesseion_start()' );
	return session_id ();
}

/**
 * 웹에서 사용하는 사용자 변수를 trim
 * @param string $value
 * @param string $defaultValue
 * @return string
 */
function request_user_var_trim($value, $defaultValue) {
	if (strlen ( $value ) == 0) return $defaultValue;
	else return $value;
}


/**
 * $_POST에서 값을 맞춰서 돌려줌
 * @param string $key
 * @param string $defaultValue
 * @param string $trim
 * @return mixed
 */
function request_post_param($key, $defaultValue = null, $trim = false) {
	if ($trim) return array_key_exists ( $key, $_POST ) ? request_user_var_trim ( $_POST [$key], $defaultValue ) : $defaultValue;
	else return array_key_exists ( $key, $_POST ) ? $_POST [$key] : $defaultValue;
}

/**
 * $_GET에서 값을 맞춰서 돌려줌
 * @param string $key
 * @param string $defaultValue
 * @param string $trim
 * @return mixed
 */
function request_get_param($key, $defaultValue = null, $trim = false) {
	if ($trim) return array_key_exists ( $key, $_GET ) ? request_user_var_trim ( $_GET [$key], $defaultValue ) : $defaultValue;
	else return array_key_exists ( $key, $_GET ) ? $_GET [$key] : $defaultValue;
}

/**
 * $_COOKIE에서 값을 맞춰서 돌려줌
 * @param string $key
 * @param string $defaultValue
 * @param string $trim
 * @return mixed
 */
function request_cookie_param($key, $defaultValue = null, $trim = false) {
	if ($trim) return array_key_exists ( $key, $_COOKIE ) ? request_user_var_trim ( $_COOKIE [$key], $defaultValue ) : $defaultValue;
	else return array_key_exists ( $key, $_COOKIE ) ? $_COOKIE [$key] : $defaultValue;
}

/**
 * $_SESSION에서 값을 맞춰서 돌려줌
 * @param string $key
 * @param string $defaultValue
 * @param string $trim
 * @return mixed
 */
function request_session_param($key, $defaultValue = null, $trim = false) {
	if ($trim) return array_key_exists ( $key, $_SESSION ) ? request_user_var_trim ( $_SESSION [$key], $defaultValue ) : $defaultValue;
	else return array_key_exists ( $key, $_SESSION ) ? $_SESSION [$key] : $defaultValue;
}

/**
 * php://input에 전체 내용을 가공없이 돌려줌.
 * @return mixed
 */
function reqeust_raw_post_data() {
	static $data = null;

	// FIXME enctype=multipart/form-data 를 지원하지 않는다. 예외 처리 해주어야 한다.
	if(is_null($data)) {
		$data = file_get_contents('php://input'); // FIXME MAGIC_QUEOTE 에 대한 대비책이 필요 하다.
	}

	return $data;
}

/**
 * php://input에 전체 내용을  array로 돌려줌
 * @return array
 */
function reqeust_parsed_raw_post_data() {
	static $data = null;

	if(is_null($data)) {
		$data = array();
		parse_str(reqeust_raw_post_data(), $data);
	}

	return $data;
}

/**
 * raw post에서 값을 돌려줌
 * @param string $key
 * @param string $defaultValue
 * @param string $trim
 * @return mixed
 */
function reqeust_raw_post_param($key, $defaultValue = null, $trim = false) {
	$reuqest_php_input = reqeust_parsed_raw_post_data();

	if ($trim) return array_key_exists ( $key, $reuqest_php_input ) ? request_user_var_trim ( $reuqest_php_input [$key], $defaultValue ) : $defaultValue;
	else return array_key_exists ( $key, $reuqest_php_input ) ? $reuqest_php_input [$key] : $defaultValue;
}

/**
 * reqeust_raw_post_param alias
 * DELETE에서 값을 돌려줌
 * @param string $key
 * @param string $defaultValue
 * @param string $trim
 * @return mixed
 */
function request_delete_param($key, $defaultValue = null, $trim = false) {
	return reqeust_raw_post_param($key, $defaultValue, $trim);
}

/**
 * reqeust_raw_post_param alias
 * PUT에서 값을 돌려줌
 * @param string $key
 * @param string $defaultValue
 * @param string $trim
 * @return mixed
 */
function request_put_param($key, $defaultValue = null, $trim = false) {
	return reqeust_raw_post_param($key, $defaultValue, $trim);
}

/**
 * 이부분은 직접 실행하지 않는다!
 * 이 라이브러리가 정상적으로 동작할수 있도록 준비하는 부분
 */
function request_lib_prepare() {
	function stripslashes_array(&$array, $iterations=0) {
		if ($iterations < 3) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					stripslashes_array($array[$key], $iterations + 1);
				} else {
					$array[$key] = stripslashes($array[$key]);
				}
			}
		}
	}

	if (get_magic_quotes_gpc()) { // FIXME always returns FALSE as of PHP 5.4.0.
		stripslashes_array($_GET);
		stripslashes_array($_POST);
		stripslashes_array($_COOKIE);
	}
}

/**
 * 요청 메서드 리턴
 * @return string
 */
function request_get_method() { return $_SERVER['REQUEST_METHOD']; }

/**
 * 요청 메서드가 get 인지
 * @return boolean
 */
function request_method_is_get() { return $_SERVER['REQUEST_METHOD'] == 'GET'; }

/**
 * 요청 메서드가 post 인지
 * @return boolean
 */
function request_method_is_post() { return $_SERVER['REQUEST_METHOD'] == 'POST'; }

/**
 * 요청 메서드가 head 인지
 * @return boolean
 */
function request_method_is_head() { return $_SERVER['REQUEST_METHOD'] == 'HEAD'; }

/**
 * 요청 메서드가 delete 인지
 * @return boolean
 */
function request_method_is_delete() { return $_SERVER['REQUEST_METHOD'] == 'DELETE'; }

/**
 * 요청 메서드가 put 인지
 * @return boolean
 */
function request_method_is_put() { return $_SERVER['REQUEST_METHOD'] == 'PUT'; }

/**
 * 요청 메서드가 options 인지
 * @return boolean
 */
function request_method_is_options() { return $_SERVER['REQUEST_METHOD'] == 'OPTIONS'; }

/**
 * 요청 메서드가 connect 인지
 * @return boolean
 */
function request_method_is_connect() { return $_SERVER['REQUEST_METHOD'] == 'CONNECT'; }

request_lib_prepare();