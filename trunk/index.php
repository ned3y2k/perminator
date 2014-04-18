<?php
require_once 'perminator.conf.php';
require_once 'func.inc.php';
require_once 'Context.php';
require_once 'lib/classes/lang/PerminatorClassLoader.php';
require_once 'lib/classes/web/script/DispatcherScript.php';

$runtimeDebug = false;

use classes\web\script\DispatcherScript;

if(isset($_SERVER) && is_array($_SERVER) && array_key_exists('DOCUMENT_ROOT', $_SERVER)) {
	error_reporting(E_ALL);
	ini_set('display_errors', 1);

	define("APP_ROOT", $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);
	define("TEST", false);
	define('DEBUG', $runtimeDebug);
} elseif(isset($_ENV) && is_array($_ENV) && array_key_exists('ZEND_PHPUNIT_PROJECT_LOCATION', $_SERVER)) { // Zend Studio Test 환경
	define("APP_ROOT", $_ENV['ZEND_PHPUNIT_PROJECT_LOCATION'].DIRECTORY_SEPARATOR);
	define("TEST", true);
	define('DEBUG', true);
} else {
	header('Content-type: text/plain; charset=utf-8');
	echo 'Runtime 환경을 충족시키지 못하였습니다'."\n";
	echo '$_SERVER 변수나 $_ENV["ZEND_PHPUNIT_PROJECT_LOCATION"]에 프로젝트 경로를 직접 넣어주십시오.'."\n";
	exit;
}
require_once 'loader.php';

if(!TEST) set_include_path(APP_ROOT);

$Dispatcher = new DispatcherScript();
$Dispatcher->doDispatch(Context::getSharedContext());

