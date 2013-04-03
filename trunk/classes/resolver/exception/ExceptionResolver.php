<?php

namespace resolver\exception;

use classes\ui\ModelMap;
interface ExceptionResolver {
	/**
	 * @param ModelMap $map
	 * @param \Exception $ex
	 * @return string
	 */
	function resolveException(ModelMap $map, \Exception $ex);
}