<?php
/**
 * Project: perminator.
 * User: Kyeongdae
 * Date: 2015-11-08
 * Time: 오전 11:41
 */

namespace classes\net;


class IPAddr {
	/**
	 * resolve client ip
	 * @return string
	 */
	public static function remoteIp() {
		if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] )) { // check ip from share internet
			$ip = $_SERVER ['HTTP_CLIENT_IP'];
		} elseif (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) { // to check ip is pass from proxy
			$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER ['REMOTE_ADDR'];
		}

		return $ip;
	}
}