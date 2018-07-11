<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-06
 * Time: 오후 2:54
 */

namespace classes\web\mvc;

interface ExceptionHandledController extends IController {
	static function handleException(\Throwable $throwable);
}