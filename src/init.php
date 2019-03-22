<?php
/** 실행 내용 검증 */
define('_VERBOSE_', false);

/** 라이브러리 경로 */
define('__PERMINATOR__', __DIR__ . DIRECTORY_SEPARATOR);
define('_SELF_', $_SERVER['PHP_SELF']);

require_once 'classes/initializer/Initializer.php';
use classes\web\IInterceptorFinder;


/** @param IInterceptorFinder $finder */
function addInterceptorFinder(IInterceptorFinder $finder) {
	DispatcherPool::get()->addInterceptorFinder($finder);
}