<?php
namespace classes\io;

use classes\io\exception\PermissionException;
use classes\io\exception\DirectoryNotFoundException;
class Directory {
	public static function create($path) {
		$parentDir = self::findParentDirectory($path);

		if(!file_exists($parentDir)) throw new DirectoryNotFoundException("상위 디렉토리 [".$parentDir."]를 찾을수 없습니다.");
		elseif(!@is_writable($parentDir)) { self::throwNewDirectoryException("상위 디렉토리 [".$parentDir, "] 디렉토리에 읽을 권한이 없습니다."); }

		mkdir($path);
	}

	public static function exist($path) {
		$parentDir = self::findParentDirectory($path);
		if(!file_exists($parentDir)) throw new DirectoryNotFoundException("상위 디렉토리 [".$parentDir."]를 찾을수 없습니다.");
		elseif(!@is_readable($parentDir)) { self::throwNewDirectoryException($parentDir, " 디렉토리에 읽을 권한이 없습니다."); }

		return file_exists($path);
	}

	private static function findParentDirectory($path) {
		$temp = str_replace('\\', '/', $path);

		$temp = explode ( '/', $path );
		unset ( $temp [count ( $temp ) - 1] );

		return implode(DIRECTORY_SEPARATOR, $temp);
	}

	private static function throwNewDirectoryException($path, $msg) {
		throw new PermissionException ( $path . $msg );

		exit;
	}
}
