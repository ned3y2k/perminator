<?php
/**
 * 클라이언트 IP를 가져온다.
 * @return string
 */
function net_client_ip() {
	if (! empty ( $_SERVER ['HTTP_CLIENT_IP'] )) { // check ip from share internet
		$ip = $_SERVER ['HTTP_CLIENT_IP'];
	} elseif (! empty ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) { // to check ip is pass from proxy
		$ip = $_SERVER ['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER ['REMOTE_ADDR'];
	}

	return $ip;
}

/**
 * 원격지의 콘텐트 로드
 * @param string $url URL
 * @return string
 */
function net_remote_resource($url) {
	$URL_parsed = parse_url ( $url );

	$host = $URL_parsed ["host"];
	$port = array_key_exists ( 'port', $URL_parsed ) ? $URL_parsed ["port"] : 80;

	$path = $URL_parsed ["path"];
	if ($URL_parsed ["query"] != "") $path .= "?" . $URL_parsed ["query"];

	$out = "GET $path HTTP/1.0\r\nHost: $host\r\n\r\n";

	$fp = fsockopen ( $host, $port, $errno, $errstr, 30 );
	if (! $fp) {
		return "$errstr ($errno)<br>\n";
	} else {
		fputs ( $fp, $out );
		$body = false;
		$in = "";

		while ( ! feof ( $fp ) ) {
			$s = fgets ( $fp, 128 );
			if ($body) $in .= $s;
			if ($s == "\r\n") $body = true;
		}
		fclose ( $fp );

		return $in;
	}
}