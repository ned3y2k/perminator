<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 4:51
 */

namespace classes\api;


use classes\api\model\JSONResult;
use classes\exception\http\HTTPResponseException;

class ActionAPIProcessor extends APIProcessor {
	/** @return JSONResult|mixed */
	final function doPerform() {
		$methodName = lcfirst(ucwords(strtolower($_SERVER[ 'REQUEST_METHOD' ]))).'Response';

		if(method_exists($this, $methodName)) {
			return $this->$methodName();
		} else {
			throw new HTTPResponseException($methodName.' method not exists.', 404);
		}
	}
}
