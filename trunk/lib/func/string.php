<?php
/**
 * 최대 길이를 넘어가는 문자열을 자름
 * @param string $str 원본 문자열
 * @param int $len 최대 길이
 * @param string $suffix 잘린 문자열 뒤에 들어갈 부분
 * @param string $charset 인코딩
 * @return string
 */
function string_cut($str, $len, $suffix = "…", $charset = 'utf-8') {
	$tmp_str = mb_substr ( $str, 0, $len, $charset );
	return $tmp_str . (mb_strlen ( $str ) > mb_strlen ( $tmp_str ) ? $suffix : "");
}