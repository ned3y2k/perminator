<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-02-09
 * Time: 오전 12:40
 */

namespace classes\web;


use classes\{
	api\model\JSONResult,
	web\response\HttpResponse
};

class JsonHttpResponse extends HttpResponse {

	/**
	 * JsonHttpResponse constructor.
	 * @param JSONResult|null $result
	 * @param bool $requireSession
	 */
	public function __construct(JSONResult $result = null, $requireSession = false) {
		parent::__construct($requireSession);

		parent::setContentType('application/json');
		$this->setBody($result);
	}

	public function setContentType($contentType) {
		throw new \UnsupportedOperationException();
	}
}