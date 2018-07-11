<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 3:46
 */

namespace classes\lang;


class StringUtil {
	/**
	 * 최대 길이를 넘어가는 문자열을 자름
	 *
	 * @param string $str     원본 문자열
	 * @param int    $len     최대 길이
	 * @param string $suffix  잘린 문자열 뒤에 들어갈 부분
	 * @param string $charset 인코딩
	 *
	 * @return string
	 */
	public static function cut($str, $len, $suffix = "…", $charset = 'utf-8') {
		$tmp_str = mb_substr($str, 0, $len, $charset);
		return $tmp_str . (mb_strlen($str) > mb_strlen($tmp_str) ? $suffix : "");
	}

	/**
	 * example)
	 * $subject = "a, b";
	 * $subject = string_replaces($subject, array('a'=>'1' 'b'=>'2'));
	 * echo $subject; // result 1, 2
	 *
	 * @param string $subject
	 * @param array  $contents
	 *
	 * @return string
	 */
	public static function replaces($subject, array $contents) {
		$temp = $subject;
		foreach ($contents as $key => $content) {
			$temp = str_replace($key, $content, $temp);
		}

		return $temp;
	}

	public static function startsWith(string $haystack, string $needle) {
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
}