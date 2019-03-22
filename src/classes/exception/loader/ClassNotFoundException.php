<?php
/**
 * User: Kyeongdae
 * Date: 2016-06-18
 * Time: 오전 12:21
 */

namespace classes\exception\loader;


class ClassNotFoundException extends \RuntimeException {
	public function __construct($message = "", $code = 0, \Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}