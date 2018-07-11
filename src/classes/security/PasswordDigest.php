<?php
namespace classes\security;

class PasswordDigest {
	/**
	 * @param string $string
	 * @param string $seed
	 *
	 * @return string length 54
	 */
	public static function digest($string, $seed) { return hash('sha384', base64_encode($seed . $string)); }
}