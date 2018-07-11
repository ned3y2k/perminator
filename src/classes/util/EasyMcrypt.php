<?php

namespace classes\util;

class EasyMcrypt {
	protected static $_openModules = array();

	/**
	 * @example EasyMcrypt::encrypt($value, $key, 'tripledes', 'ecb'),
	 * @param string $string
	 * @param string $key
	 * @param string $type
	 * @param string $mode
	 * @return string
	 * @throws \Exception
	 */
	public static function encrypt($string, $key, $type, $mode) {
		$module = self::_getModule($type, $mode);
		$iv = self::_alt_mcrypt_create_iv(mcrypt_enc_get_iv_size($module));
		mcrypt_generic_init($module, $key, $iv);
		$data = mcrypt_generic($module, $string);
		mcrypt_generic_deinit($module);
		return $data;
	}

	/**
	 * @example EasyMcrypt::decrypt($value, $key, 'tripledes', 'ecb')
	 * @param string $string
	 * @param string $key
	 * @param string $type
	 * @param string $mode
	 * @return string
	 * @throws \Exception
	 */
	public static function decrypt($string, $key, $type, $mode) {
		$module = self::_getModule($type, $mode);
		$iv = self::_alt_mcrypt_create_iv(mcrypt_enc_get_iv_size($module));
		mcrypt_generic_init($module, $key, $iv);
		$data = trim(mdecrypt_generic($module, $string));
		mcrypt_generic_deinit($module);
		return $data;
	}

	/**
	 * @param $type
	 * @param $mode
	 * @return mixed
	 * @throws \Exception
	 */
	protected static function _getModule($type, $mode) {
		if (!isset (self::$_openModules [$type] [$mode])) {
			if (in_array($type, mcrypt_list_algorithms()) && in_array($mode, mcrypt_list_modes())) {
				self::$_openModules [$type] [$mode] = mcrypt_module_open($type, '', $mode, '');
			} else {
				throw new \Exception ("{$type} is not a valid algorithm");
			}
		}

		return self::$_openModules [$type] [$mode];
	}

	/**
	 * borrowed from http://www.php.net/manual/en/function.mcrypt-create-iv.php#54925 *
	 * @param $size
	 * @return string
	 */
	protected static function _alt_mcrypt_create_iv($size) {
		$iv = '';
		for ($i = 0; $i < $size; $i++) {
			$iv .= chr(rand(0, 255));
		}
		return $iv;
	}
}

/*
$string = 'Please encrypt me';
$key = 'this is my encryption key';
$type = 'tripledes';
$mode = 'ecb'; // cbc cfb ctr ecb ncfb nofb ofb stream

$encrypted = easyMcrypt::encrypt($string, $key, $type, $mode);
var_dump($encrypted);

$decrypted = easyMcrypt::decrypt($encrypted, $key, $type, $mode);
var_dump($decrypted);
*/