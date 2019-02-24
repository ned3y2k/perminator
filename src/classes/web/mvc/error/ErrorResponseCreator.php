<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2019-02-24
 * Time: 오후 6:00
 */

namespace classes\web\mvc\error;


use classes\context\ApplicationContext;
use classes\web\response\HttpResponse;

class ErrorResponseCreator implements IErrorResponseCreator {
	static function create(ApplicationContext $applicationContext, \Throwable $ex): HttpResponse {
		$httpResponse = new HttpResponse();
		$httpResponse->status($ex->getCode());
		$httpResponse->setContentType('text/plain');
		
		$body = $ex->getMessage()."\n\n";
		$body .= $ex->getTraceAsString();
		$httpResponse->setBody($body);

		return $httpResponse;
	}
}