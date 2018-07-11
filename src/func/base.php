<?php

class UnsupportedOperationException extends RuntimeException { }

class ExceptionPageNotFound extends RuntimeException { }

/**
 *
 * @return string
 */
function initCacheDir() {
	$cacheDir = substr(_DIR_CACHE_ROOT_, 0, -1);
	if (!file_exists($cacheDir)) mkdir($cacheDir, 7666);

	return $cacheDir;
}

/**
 * 예외발생: 구현되지 않은 동작
 *
 * @throws UnsupportedOperationException
 */
function throwNewUnimplementedException() {
	$trace = debug_backtrace();
	throw new UnsupportedOperationException ('UnimplementedException. '.$trace[0]['file'].':'.$trace[0]['line']);
}

/**
 * 예외발생: 지원되지 않는 동작
 *
 * @throws UnsupportedOperationException
 */
function unsupportedOperation() {
	$trace = debug_backtrace();
	throw new UnsupportedOperationException ("지원되지 않는 기능 입니다. ".$trace[0]['file'].':'.$trace[0]['line']);
}

/**
 * 경로를 만들어준다.
 * PHP 코드가 기본적으로 utf-8로 되어 있는데 요놈을 _FILE_SYSTEM_ENCODING_ 인코딩으로 변환
 *
 * @param string $filePath
 * @return string
 */
function create_path($filePath) {
	$filePath = str_replace("\\", DIRECTORY_SEPARATOR, $filePath);
	$filePath = str_replace("/", DIRECTORY_SEPARATOR, $filePath);

	if (_FILE_SYSTEM_ENCODING_ == 'utf-8') return $filePath;

	return iconv('utf-8', _FILE_SYSTEM_ENCODING_, $filePath);
}

/**
 * 숫자를 한글 발음 표기
 *
 * @param int|string $number
 * @return string
 */
function number_format_korean($number) {
	if (is_int($number))
		$number = (string)$number;

	$num   = array('', '일', '이', '삼', '사', '오', '육', '칠', '팔', '구');
	$unit4 = array('', '만', '억', '조', '경');
	$unit1 = array('', '십', '백', '천');

	$res = array();

	$number = str_replace(',', '', $number);
	$split4 = str_split(strrev((string)$number), 4);

	for ($i = 0; $i < count($split4); $i++) {
		$temp   = array();
		$split1 = str_split((string)$split4[$i], 1);

		$len = count($split1);
		$end = $len - 1;
		for ($j = 0; $j < $len; $j++) {
			$u = (int)$split1[$j];
			if ($u > 0) {
				$temp[] = ($j == $end || $j == 0)
					? $num[$u] . $unit1[$j]
					: $unit1[$j];
			}
		}
		if (count($temp) > 0) $res[] = implode('', array_reverse($temp)) . $unit4[$i];
	}

	return implode('', array_reverse($res));
}

/**
 * php 사이즈 표현을 float으로 표현
 *
 * @param string $size
 * @return float
 */
function parse_size($size) {
	$unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
	$size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
	if ($unit) {
		// Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
		return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
	} else {
		return round($size);
	}
}

/**
 * 변수를 복사
 * @param mixed $var
 * @return mixed
 */
function safe_copy($var) {
	if(is_object($var)) {
		return clone $var;
	} elseif(is_array($var)) {
		$newVar = array();
		foreach($var as $nested) {
			$newVar[] = safe_copy($nested);
		}
	} else {
		return $var;
	}

	return null;
}