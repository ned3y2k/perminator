<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 4:49
 */

namespace classes\exception\mvc;


use classes\exception\http\HTTPResponseException;

class ActionControllerResponseException extends HTTPResponseException
{
	public function __construct($message = "", $code = 0, $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}
