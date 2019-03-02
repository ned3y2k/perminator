<?php
/**
 * User: Kyeongdae
 * Date: 2018-08-09
 * Time: 오전 6:41
 */

namespace classes\web\response;



interface IResponseDelegate {
	function createResponse(): HttpResponse;
}