<?php
/**
 * Project: perminator.
 * User: Kyeongdae
 * Date: 2015-11-08
 * Time: 오전 11:40
 */

namespace classes\net;


class WebContentUtil {
	/**
	 * http 파일을 다운로드
	 *
	 * @param string $path 저장할 경로
	 * @param string $url  url
	 * @param int    $port
	 */
	public static function download($path, $url, $port = 80) {
		// FIXME return에 http response code 필요함
		$ch = curl_init($url);
		$fp = fopen($path, 'wb');
		curl_setopt($ch, CURLOPT_PORT , $port);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}

	public static function read($url, $port = 80) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_PORT , $port);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FILE, null);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	public static function downloadFromSSL($path, $url, $rootPemPath, $port = 443) {
		$fp = fopen($path, 'wb');

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_PORT , $port);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CAINFO, $rootPemPath);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLINFO_HEADER_OUT, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
		curl_setopt($ch, CURLOPT_FILE, $fp);

		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}

	public static function readFromSSL($url, $rootPemPath, $port = 443) {
		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_PORT , $port);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_CAINFO, $rootPemPath);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLINFO_HEADER_OUT, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}