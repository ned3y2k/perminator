<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 11:19
 */

namespace classes\lang;

use classes\exception\error\WarningException;
use classes\exception\loader\PHPScriptNotFoundException;

class LibraryLoader {
	public function load(string $relativeName) {
		$path = _APP_ROOT_ . str_replace('/', "\\", $relativeName) . '.php';
		try {
			/** @noinspection PhpIncludeInspection */
			include_once $path;
		} catch (WarningException $e) {
			throw new PHPScriptNotFoundException($path);
		}
	}
}