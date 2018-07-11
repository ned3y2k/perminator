<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-09-23
 * 시간: 오후 1:24
 */
namespace classes\web\mvc;

use classes\context\IApplicationContext;
use classes\web\HttpResponse;

/**
 * Interface IController
 * @package classes\web\mvc
 */
interface IController {
	/**
	 * @param IApplicationContext $applicationContext
	 *
	 * @return void
	 */
	function setApplicationContext(IApplicationContext $applicationContext);

	/** @return string[] */
	static function requireLibs();

	/** @return IPageBuilder|HttpResponse */
	function handleRequest();
}