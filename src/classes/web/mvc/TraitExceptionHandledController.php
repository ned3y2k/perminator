<?php
/**
 * User: Kyeongdae
 * Date: 2018-12-15
 * Time: 오후 11:35
 */

namespace classes\web\mvc;


use classes\context\IApplicationContext;

trait TraitExceptionHandledController {
	abstract function handleException(IApplicationContext $applicationContext, \Throwable $throwable);
}