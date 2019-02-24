<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-06
 * Time: 오후 5:20
 */

namespace classes\web\dispatch\factory;


use classes\{context\IApplicationContext, web\dispatch\controller\error\ErrorController, web\mvc\IController};

class ControllerFactory {
	/**
	 * @param string              $fullName
	 * @param IApplicationContext $applicationContext
	 *
	 * @return IController
	 */
	public function createControllerInstance($fullName, IApplicationContext $applicationContext) {
		try {
			$this->loadAndCheckControllerClass($fullName);

			/** @var IController $controller instance */
			$controller = new $fullName();
		} catch (\Throwable $ex) {
			if ($ex instanceof \InvalidArgumentException) {
				$code = 404;
			} else {
				$code = 500;
			}

			$controller = new ErrorController($ex, $code);
		}

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
				throw new \RuntimeException($fullName . " is not implements IController");
			}
		} catch (\Throwable $ex) {
			if (getApplicationContext()->getDebugContext()->available()) {
				throw new \InvalidArgumentException($fullName . ' class not found');
			}
		}
	}
}