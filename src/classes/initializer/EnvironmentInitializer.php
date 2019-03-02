<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 4:41
 */

namespace classes\initializer;

use classes\io\exception\PermissionException;
use classes\lang\ArrayUtil;

class EnvironmentInitializer implements Initializer {
	/**
	 * @throws PermissionException
	 */
	public function init() {
		//require_once __PERMINATOR__ . 'func/php.ext.php';
		require_once __PERMINATOR__ . 'func/base.php';
		require_once __PERMINATOR__ . 'classes/lang/ArrayUtil.php';

		if (TEST) {
			$this->injectGlobalVariables();
		} else {
			//	$this->assertIniValue('allow_url_fopen', 0, "does not want on");
			$this->checkPhpIni();
		}

		$this->determineFileSystemEncoding();
		$this->setEncodingConstant();
		$this->createInternalDirs();
		$this->dismissMagicGpcQuotes();
	}

	/**
	 * @throws PermissionException
	 */
	private function createInternalDirs() {
		foreach (ArrayUtil::getValue(get_defined_constants(true), 'user') as $name => $const) {
			if (strpos($name, '_DIR_') === 0 && !file_exists($const)) {
				$parentDir = dirname($const);

				if (!is_writable($parentDir))
					throw new PermissionException($parentDir . " not has write permission");
				mkdir($const, 7666, true);
			}
		}
	}

	/**
	 * 파일 시스템 인코딩 윈도우인 경우는 euc-kr.
	 * 나머지인 경우는 utf-8로 결정
	 */
	private function determineFileSystemEncoding() {
		if (PHP_OS == 'WINNT') $file_system_encoding = 'euc-kr';
		else $file_system_encoding = 'utf-8';

		define('_FILE_SYSTEM_ENCODING_', $file_system_encoding);
	}

	private function setEncodingConstant() {
		if (!defined('_DEFAULT_CHARSET_')) {
			/** 기본 인코딩 */
			define('_DEFAULT_CHARSET_', 'utf-8');
		}
	}

	private function dismissMagicGpcQuotes() {
		if (get_magic_quotes_gpc()) {
			/** @noinspection PhpDeprecationInspection */
			@set_magic_quotes_runtime(0);
			ini_set('magic_quotes_runtime', 0);

			function stripslashes_gpc(&$value) {
				$value = stripslashes($value);
			}

			array_walk_recursive($_GET, 'stripslashes_gpc');
			array_walk_recursive($_POST, 'stripslashes_gpc');
			array_walk_recursive($_COOKIE, 'stripslashes_gpc');
			array_walk_recursive($_REQUEST, 'stripslashes_gpc');
		}
	}

	private function injectGlobalVariables() {
		global $argv;
		$GLOBALS["headers"] = array();
		$func = '
	$header = array();
	$headerStrings = explode(":", $string);
	$header["string"] = trim($headerStrings[1]);
	$header["replace"] = $replace;
	$header["http_response_code"] = $http_response_code;

	$GLOBALS["headers"][trim($headerStrings[0])] = $header;
';

		if (php_sapi_name() === 'cli' && count($argv) > 1) {
			parse_str($argv[1], $parsedArgv);
			foreach ($parsedArgv as $key => $val) {
				$_GET[$key] = $val;
				$_POST[$key] = $val;
				$_REQUEST[$key] = $val;
				$_SESSION[$key] = $val;
				$_COOKIE[$key] = $val;
			}
		}

		if (!array_key_exists('HTTP_USER_AGENT', $_SERVER))
			$_SERVER['HTTP_USER_AGENT'] = $_SERVER['ComSpec'] . ';' . php_uname('a');
		if (!array_key_exists('HTTP_HOST', $_SERVER))
			$_SERVER['HTTP_HOST'] = 'localhost';
	}

	private function checkPhpIni() {
		$this->assertIniValue('allow_url_include', 0, "does not want on");
		$this->assertIniValue('short_open_tag', 1);
		$this->assertIniValue('register_globals', 0, "does not want on");
	}

	/**
	 * PHP 환경 변수를 검사 하고 예상 되는 내용이 없다면 메시지를 출력하고 예외 발생
	 *
	 * @param string $php_value
	 * @param string $expect
	 * @param string $fail_msg
	 */
	private function assertIniValue($php_value, $expect, $fail_msg = "") {
		if (ini_get($php_value) != $expect) {
			http_response_code(500);
			header('content-type: text/plain; charset=utf-8');

			echo php_ini_loaded_file() . "\n";
			echo "required set {$php_value} value {$expect}.\n{$fail_msg}";
			echo "\n" . $this->phpIniSettingHint($php_value, $expect);
			exit;
		}
	}

	/**
	 * PHP 설정 안내 메시지
	 *
	 * @param string $php_value
	 * @param string $value
	 *
	 * @return string
	 */
	private function phpIniSettingHint($php_value, $value): string {
		return "add the following line \"PHP_VALUE {$php_value} {$value}\" on .htaccess";
	}
}