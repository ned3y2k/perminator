<?php
namespace classes\resolver\request;

use classes\content\Context;
interface RequestResolver {
	/**
	 * @param Context $context
	 * @return \classes\web\script\View
	 * @throws \Exception
	 */
	public function resolve(Context $context);

	public function findAllModelMap();
}