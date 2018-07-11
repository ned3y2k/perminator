<?php
namespace classes\io;

use classes\io\exception\PermissionException;

class Directory {
	/**
	 * @param $path
	 * @throws PermissionException
	 */
	public static function create($path) {
		$parentDir = self::findParentDirectory($path);

		if (file_exists($parentDir) && !@is_writable($parentDir)) {
			self::throwNewDirectoryException($parentDir, "Not Have Permission to read the parent directory [{$parentDir}]");
		}

		mkdir($path, 7666, true);
	}

	/**
	 * @param $path
	 * @return bool
	 * @throws PermissionException
	 */
	public static function exist($path) {
		$parentDir = self::findParentDirectory($path);
		if (file_exists($parentDir) && !@is_readable($parentDir)) {
			self::throwNewDirectoryException($parentDir, " 디렉토리에 읽을 권한이 없습니다.");
		}

		return file_exists($path);
	}

	private static function findParentDirectory($path) {
		$temp = str_replace('\\', '/', $path);

		$temp = explode('/', $temp);
		unset ($temp [ count($temp) - 1 ]);

		return implode(DIRECTORY_SEPARATOR, $temp);
	}

	/**
	 * @param $path
	 * @param $msg
	 * @throws PermissionException
	 */
	private static function throwNewDirectoryException($path, $msg) { throw new PermissionException ($path . $msg); }

	public static function relativePath($root, $path) {
		$path = normalizepath($path);
		$root = normalizepath($root);

		return str_replace($root, '', $path);
	}
}
