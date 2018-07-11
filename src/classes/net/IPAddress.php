<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-07
 * Time: 오후 3:19
 */

namespace classes\net;


use LogicException;

class IPAddress {
	/**
	 * 클라이언트 IP를 가져온다.
	 *
	 * @param bool $hex
	 *
	 * @return string
	 */
	public static function remoteIp(bool $hex = false) {
		if (!empty ($_SERVER ['HTTP_CLIENT_IP'])) { // check ip from share internet
			$ip = $_SERVER ['HTTP_CLIENT_IP'];
		} elseif (!empty ($_SERVER ['HTTP_X_FORWARDED_FOR'])) { // to check ip is pass from proxy
			$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
		} elseif (array_key_exists('REMOTE_ADDR', $_SERVER)) {
			$ip = $_SERVER ['REMOTE_ADDR'];
		} elseif (TEST) {
			$ip = "0.0.0.0";
		} else {
			throw new LogicException();
		}

		if ($ip == '::1')
			$ip = '127.0.0.1';

		if (!$hex)
			return $ip;
		else
			return self::toHex($ip);
	}

	public static  function toHex($ipv4) {
		if(is_string($ipv4)) $ipv4 = explode('.', $ipv4);

		foreach ($ipv4 as $key => $no) {
			$ipv4[ $key ] = strtoupper(sprintf("%02x", $no));
		}
		$ipv4 = implode('', $ipv4);

		return $ipv4;
	}

	public static function net_hex_ip_to_dec($hex) {
		$ipString = sprintf(
			'%d.%d.%d.%d',
			hexdec(substr($hex, 0, 2)),
			hexdec(substr($hex, 2, 2)),
			hexdec(substr($hex, 4, 2)),
			hexdec(substr($hex, 6, 2))
		);

		return $ipString;
	}
}