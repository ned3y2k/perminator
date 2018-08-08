<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-06
 * Time: 오후 2:54
 */

namespace classes\web\mvc;

use classes\{
	context\IApplicationContext,
	web\response\HttpResponse
};

interface IExceptionHandledController extends IController {
	static function handleException(IApplicationContext $applicationContext, \Throwable $throwable): HttpResponse;
}