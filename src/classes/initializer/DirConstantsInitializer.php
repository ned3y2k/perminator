<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-10
 * Time: 오후 12:12
 */

namespace classes\initializer;


class DirConstantsInitializer implements Initializer {

	public function init() {
		$this->initAppRootAndTestConstant();

		if (!TEST) {
			$this->initDirConstants();
		} else {
			$this->initTestDirConstants();
		}
	}

	private function initAppRootAndTestConstant() {
		error_reporting(E_ALL);
		ini_set('display_errors', 1);

		$app_root = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;

		/** 앱 루트 경로 */
		define("_APP_ROOT_", $app_root);

		set_include_path(get_include_path() . PATH_SEPARATOR . _APP_ROOT_ . PATH_SEPARATOR);
		if (!TEST) set_include_path(get_include_path() . PATH_SEPARATOR . _APP_ROOT_);
	}

	private function initDirConstants() {
		/** 웹 사이트 사용자 업로드 파일 경로 */
		define('_DIR_USR_', _APP_ROOT_ . 'usr' . DIRECTORY_SEPARATOR);
		define('_URL_USR_', '/usr/');

		/** 사이트 파일 시스템 저장 변수 경로 */
		define('_DIR_VAR_', _APP_ROOT_ . 'var' . DIRECTORY_SEPARATOR);

		/** 캐쉬 루트 경로 */
		define('_DIR_CACHE_ROOT_', _APP_ROOT_ . 'cache' . DIRECTORY_SEPARATOR);

		/** 로그 루트 경로 */
		define('_DIR_LOG_', _DIR_VAR_ . 'log' . DIRECTORY_SEPARATOR);

		/** PHP 기본 로그 경로 */
		define('_DIR_LOG_PHP_', _DIR_LOG_ . 'php' . DIRECTORY_SEPARATOR);

		/** 예외 기본 로그 경로 */
		define('_DIR_LOG_PHP_EXCEPTION_', _DIR_LOG_ . 'exception' . DIRECTORY_SEPARATOR);

		/** 사용자 로그 경로 */
		define('_DIR_LOG_PHP_USR_', _DIR_LOG_ . 'usr' . DIRECTORY_SEPARATOR);
	}

	private function initTestDirConstants() {
		define('_DIR_TEST_ROOT_', _APP_ROOT_ . 'test' . DIRECTORY_SEPARATOR);

		/** 웹 사이트 사용자 업로드 파일 경로 */
		define('_DIR_USR_', _DIR_TEST_ROOT_ . 'usr' . DIRECTORY_SEPARATOR);
		define('_URL_USR_', '/test/usr/');

		/** 사이트 파일 시스템 저장 변수 경로 */
		define('_DIR_VAR_', _DIR_TEST_ROOT_ . 'var' . DIRECTORY_SEPARATOR);

		/** 캐쉬 루트 경로 */
		define('_DIR_CACHE_ROOT_', _DIR_TEST_ROOT_ . 'cache' . DIRECTORY_SEPARATOR);

		/** 로그 루트 경로 */
		define('_DIR_LOG_', _DIR_VAR_ . 'log' . DIRECTORY_SEPARATOR);

		/** PHP 기본 로그 경로 */
		define('_DIR_LOG_PHP_', _DIR_LOG_ . 'php' . DIRECTORY_SEPARATOR);

		/** 예외 기본 로그 경로 */
		define('_DIR_LOG_PHP_EXCEPTION_', _DIR_LOG_ . 'exception' . DIRECTORY_SEPARATOR);

		/** 사용자 로그 경로 */
		define('_DIR_LOG_PHP_USR_', _DIR_LOG_ . 'usr' . DIRECTORY_SEPARATOR);

		ini_set('error_log', _DIR_LOG_PHP_ . DIRECTORY_SEPARATOR . date('Ymd') . '.log');
		ini_set('log_errors', 1);
	}
}