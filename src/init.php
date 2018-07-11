<?php
/** 실행 내용 검증 */
define('_VERBOSE_', false);

/** 라이브러리 경로 */
define('__PERMINATOR__', __DIR__ . DIRECTORY_SEPARATOR);

require_once 'classes/initializer/Initializer.php';
use classes\web\IInterceptorFinder;


/** @param IInterceptorFinder $finder */
function addInterceptorFinder(IInterceptorFinder $finder) {
	DispatcherPool::get()->addInterceptorFinder($finder);
}