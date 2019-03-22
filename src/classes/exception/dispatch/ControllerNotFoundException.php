<?php
/**
 * User: Kyeongdae
 * Date: 2019-02-24
 * Time: 오후 8:51
 */

namespace classes\exception\dispatch;


use Throwable;

class ControllerNotFoundException extends ControllerFactoryException {

	/**
	 * @var string
	 */
	private $className;

	public function __construct(string $className = "", int $code = 0, Throwable $previous = null) {
		parent::__construct($className . ' controller class not found', $code, $previous);
		$this->className = $className;
	}

	/**
	 * @return string
	 */
	public function getClassName(): string {
		return $this->className;
	}
}