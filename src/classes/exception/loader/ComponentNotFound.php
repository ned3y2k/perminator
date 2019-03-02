<?php
/**
 * User: Kyeongdae
 * Date: 2018-06-30
 * Time: 오후 3:06
 */

namespace classes\exception\loader;


class ComponentNotFound extends \RuntimeException {
	public function __construct($message = "", $code = 0, \Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}