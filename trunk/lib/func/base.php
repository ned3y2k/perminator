<?php
class UnsupportedOperationException extends RuntimeException { }
class ExceptionPageNotFound extends RuntimeException {};

if (! defined ( 'BITMOBILE' )) throw new LogicException('not defined BITMOBILE');

function create_site_link($addr) {
	return $addr;
}

function array_is_assoc(array $arr) {
	return array_keys($arr) !== range(0, count($arr) - 1);
}

class PrimitiveValueHolder {
	private $value;

	function __construct($value) {
		$this->value = $value;
	}

	function getValue() {
		return $this->value;
	}
}
function primitive_value_copy($value) {
	$holder = new PrimitiveValueHolder($value);
	$newHolder = clone $holder;

	return $newHolder->getValue();
}
function array_copy(array $array) {
	$newArray = array();

	foreach ($array as $key => $element) {
		$newArray[$key] = is_object($element) ? clone $element : primitive_value_copy($element);
	}

	return $newArray;
}

/**
 * 인스턴스의 필드를 배열로 변환
 * @param string $data
 * @return mixed[]
 */
function object_to_array($data) {
	if (is_array ( $data ) || is_object ( $data )) {
		$result = array ();
		foreach ( $data as $key => $value ) $result [$key] = object_to_array ( $value );
		return $result;
	}
	return $data;
}

/**
 * 필드에 엑세스 하는 클로져 생성
 * @param string $name
 * @return \Closure
 */
function field_resolver($name) { return create_function ( '$instance', 'return is_null($instance) ? null : $instance->' . $name . ';' ); }

/**
 * 프로퍼티(겟터)에 엑세스 하는 클로져 생성
 * @param string $name
 * @return \Closure
 */
function getter_resolver($name) { return create_function ( '$instance', 'return is_null($instance) ? null : $instance->' . $name . '();' ); }

/**
 *
 * @return string
 */
function initCacheDir() {
	$cacheDir = substr(CACHE_PATH, 0,-1);
	if (! file_exists ( $cacheDir )) mkdir ( $cacheDir );

	return $cacheDir;
}

/**
 * 예외발생: 구현되지 않은 동작
 * @throws RuntimeException
 */
function throwNewUnimplementedException() { throw new RuntimeException ( 'UnimplementedException' ); }

/**
 * 예외발생: 지원되지 않는 동작
 * @throws UnsupportedOperationException
 */
function unsupported_operation() { throw new UnsupportedOperationException ( "지원되지 않는 기능 입니다." ); }

/**
 * 디버그를 하기위하여 표시모드를 텍스트로 바꿈
 * @param string $encoding인코딩
 */
function switch_to_text_mode($encoding='utf-8') {
	header("content-type: text/plain;charset={$encoding}");
}

/**
 * 패리티 수를 만든다
 * @param string $data
 * @return int [0|1]
 */
function make_parity($data) { return hexdec(bin2hex($data)) % 2 == 0 ? 1 : 0; }


class ParityCheckException extends InvalidArgumentException{}
/**
 * 패리티 수를 점검한다
 * @param int $parity [0|1]
 * @param string $data
 * @return boolean
 */
function check_parity($parity, $data) { if(!is_numeric($parity)) new ParityCheckException('invalid parity value'); return make_parity($data) == $parity; }