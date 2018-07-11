<?php
/**
 * User: Kyeongdae
 * Date: 2016-11-13
 * Time: 오전 2:43
 */

namespace classes\api\model;


class TestJsonResult extends JSONResult{
	public static function create(JSONResult $result) {
		if(!defined('BitTestCaseDefined'))
			throw new \RuntimeException('not test env');

		$r = new TestJsonResult();
		$r->data = $result->data;
		$r->errorMsg = $result->errorMsg;
		$r->errorClassName = $result->errorClassName;
		$r->errorCode = $result->errorCode;
		$r->data = $result->data;
		$r->dataSetLock = $result->dataSetLock;
		$r->extraData = $result->extraData;
		$r->stringier = $result->stringier;

		return $r;
	}

	/** @return mixed */
	public function getData() {
		return $this->data;
	}
}