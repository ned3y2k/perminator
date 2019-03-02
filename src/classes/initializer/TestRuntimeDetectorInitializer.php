<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-11
 * Time: 오후 4:50
 */

namespace classes\initializer;


class TestRuntimeDetectorInitializer implements Initializer {

	public function init() {
		$launchScript = array_key_exists('argv', $GLOBALS) ? $GLOBALS['argv'][0] : '';
		$launchScriptIsComposerPhpUnit = basename($launchScript) == 'phpunit' && strpos($launchScript, 'vendor') !== false;

		if (php_sapi_name() == 'cli' && $launchScriptIsComposerPhpUnit) {
			define('TEST', true);

			$this->initConstants($launchScript, $launchScriptIsComposerPhpUnit);
		} else {
			define('TEST', false);
		}
	}

	private function initConstants($launchScript, $launchScriptIsComposerPhpUnit) {
		if (isset($_ENV) && is_array($_ENV) && array_key_exists('ZEND_PHPUNIT_PROJECT_LOCATION', $_SERVER)) { // Zend Studio Test 환경
			$_SERVER['DOCUMENT_ROOT'] = $_SERVER['ZEND_PHPUNIT_PROJECT_LOCATION'];
		} elseif (getenv('PROJECT_LOCATION') !== false) { // 기타 테스트 환경
			$_SERVER['DOCUMENT_ROOT'] = getenv('PROJECT_LOCATION');
		} elseif ($launchScriptIsComposerPhpUnit) {
			$_SERVER['DOCUMENT_ROOT'] = realpath(dirname($launchScript)."/../../../");
		} else {
			echo 'Runtime 환경을 충족시키지 못하였습니다' . "\n";
			echo '$_SERVER 변수나 $_ENV["PROJECT_LOCATION"]에 프로젝트 경로를 직접 넣어주십시오.' . "\n";
			die();
		}
	}
}