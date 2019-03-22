<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-06
 * Time: 오후 5:20
 */

namespace classes\web\dispatch\factory;


use classes\{context\IApplicationContext,
	exception\dispatch\ControllerNotFoundException,
	exception\dispatch\NotControllerException,
	web\mvc\IController};

class ControllerFactory {
	/**
	 * @param string              $fullName
	 * @param IApplicationContext $applicationContext
	 *
	 * @return IController
	 * @throws \Throwable
	 */
	public function createControllerInstance($fullName, IApplicationContext $applicationContext): IController {
		$this->loadAndCheckControllerClass($fullName);

		/** @var IController $controller instance */
		$controller = new $fullName();

		$controller->setApplicationContext($applicationContext);
		$controller->onCreate();

		return $controller;
	}

	/**
	 * @param $fullName
	 *
	 * @throws \Throwable
	 */
	private function loadAndCheckControllerClass($fullName) {
		try {
			$implements = class_implements($fullName);
			$check      = key_exists('classes\web\mvc\IController', $implements);

			if (!$check) {
				throw new NotControllerException($fullName);
			}
		} catch (\Throwable $ex) {
			if (getApplicationContext()->getDebugContext()->available()) {
				throw new ControllerNotFoundException($fullName);
			}
		}
	}
}