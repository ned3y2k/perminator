<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 11:19
 */

namespace classes\pool;

use classes\lang\LibraryLoader;

class LibraryLoaderPool {
	private static $instance;

	public static function getInstance(): LibraryLoader {
		if (!self::$instance)
			self::$instance = new LibraryLoader();

		return self::$instance;
	}

}