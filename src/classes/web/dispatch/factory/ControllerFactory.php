<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-06
 * Time: 오후 5:20
 */

namespace classes\web\dispatch\factory;


use classes\{
	context\IApplicationContext, web\mvc\IController
};

class ControllerFactory {
	/**
	 * @param string $fullName
	 * @param IApplicationContext $applicationContext
	 *
	 * @return IController
	 */
	public function createControllerInstance($fullName, IApplicationContext $applicationContext) {
		$this->loadAndCheckControllerClass($fullName);

		/** @var IController $controller instance */
		$controller = new $fullName();
		$controller->setApplicationContext($applicationContext);
		$controller->onCreate();

		return $controller;
	}

	/**
	 * @param $fullName
	 */
	private function loadAndCheckControllerClass($fullName) {
		$implements = class_implements($fullName);
		$check = key_exists('classes\web\mvc\IController', $implements);

		if (!$check) {
			throw new \RuntimeException($fullName . " is not implements IController");
		}
	}
}