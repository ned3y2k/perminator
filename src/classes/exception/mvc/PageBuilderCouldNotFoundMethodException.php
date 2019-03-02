<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-07
 * Time: 오후 3:41
 */

namespace classes\exception\mvc;


class PageBuilderCouldNotFoundMethodException extends PageBuilderException {
	private $className;
	private $methodName;

	public function __construct($className, $methodName) {
		$this->className  = $className;
		$this->methodName = $methodName;

		$this->message = $className . "->" . $methodName;
	}
}