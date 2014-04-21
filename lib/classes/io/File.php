<?php
namespace classes\io;
use classes\io\exception\IOException;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\FileNotFoundException;
use classes\io\exception\PermissionException;

class File {
	public static function getFileExtName($fileName) {
		if(!is_file($fileName)) throw new  IOException("파일이 아닙니다.", IOException::CODE_NOT_VALID);
		elseif(!@is_readable($fileName)) { throw new PermissionException('파일을 읽을수 없습니다.'); }
		return pathinfo ( $fileName, PATHINFO_EXTENSION );
	}

	public static function copy($sourceFilePath, $destFilePath, $overwrite = true) {
		if($overwrite && file_exists($destFilePath)) { throw new IOException('대상 파일이 이미 존재합니다.', IOException::CODE_EXIST_FILE); }
		elseif(!file_exists(dirname($destFilePath))) { throw new DirectoryNotFoundException('대상 디렉토리가 존재하지 않습니다.'); }
		elseif(!file_exists($sourceFilePath)) { throw new FileNotFoundException("소스 파일({$sourceFilePath})을 찾을 수 없습니다."); }
		elseif(!@is_readable($sourceFilePath)) { throw new PermissionException('원본 파일을 읽을수 없습니다.'); }
		elseif(!@is_writable($destFilePath)) { throw new PermissionException('대상 파일을 기록할수 없습니다.'); }

		if(!copy($sourceFilePath, $destFilePath)) { throw new IOException('알수 없는 에러', IOException::CODE_UNKNOWN); }
	}

	public static function move($sourceFilePath, $destFilePath) {
		if(file_exists($destFilePath)) { throw new IOException('대상 파일이 이미 존재합니다.', IOException::CODE_EXIST_FILE); }
		elseif(!file_exists(dirname($destFilePath))) { throw new DirectoryNotFoundException('대상 디렉토리가 존재하지 않습니다.'); }
		elseif(!file_exists($sourceFilePath)) { throw new FileNotFoundException("소스 파일({$sourceFilePath})을 찾을 수 없습니다."); }
		elseif(!@is_readable($sourceFilePath)) { throw new PermissionException('원본 파일을 읽을수 없습니다.'); }
		elseif(!@is_writable($destFilePath)) { throw new PermissionException('대상 파일을 기록할수 없습니다.'); }

		if(!copy($sourceFilePath, $destFilePath)) { throw new IOException('알수 없는 에러', IOException::CODE_UNKNOWN); }
	}


	/**
	 * 파일에 내용을 추가한다
	 * @param string $filePath
	 * @param string $content
	 * @param $lock
	 * @param $fileUseIncludePath
	 * @throws \InvalidArgumentException
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public static function appendAlltext($filePath, $content, $lock = false, $fileUseIncludePath = false ) {
		$flag = FILE_APPEND;
		if($lock == true)
			$flag = LOCK_EX;
		if($fileUseIncludePath == true)
			$flag = $flag | FILE_USE_INCLUDE_PATH;

		self::writeAllText($filePath, $content, $flag);
	}

	/**
	 * 기존 파일에 내용을 덮어쓴다.
	 * @param string $filePath
	 * @param string $content
	 * @param number $flag
	 * @param $lock
	 * @param $fileUseIncludePath
	 * @throws \InvalidArgumentException
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public static function writeAllText($filePath, $content, $lock = false, $fileUseIncludePath = false ) {
		if(!is_scalar($content)) { throw new \InvalidArgumentException('content is an integer, floating point, string, or Boolean only'); }
		elseif(!file_exists(dirname($filePath))) { throw new DirectoryNotFoundException('디렉토리가 존재하지 않습니다.'); }
		elseif(file_exists($filePath) && !@is_writable($filePath)) { throw new PermissionException("{$filePath} 파일을 기록할수 없습니다."); }

		$flag = 0;
		if($lock == true)
			$flag = LOCK_EX;
		if($fileUseIncludePath == true)
			$flag = $flag | FILE_USE_INCLUDE_PATH;

		file_put_contents($filePath, $content, $flag);
	}

	public static function readAllLine($path, $use_include_path = false) {
		if(!file_exists($path)) { throw new FileNotFoundException("파일({$path})을 찾을 수 없습니다."); }
		elseif(!@is_readable($path)) { throw new FileNotFoundException("파일({$path})을 찾을 수 없습니다."); }

		return file_get_contents($path, $use_include_path);
	}

	/**
	 * @deprecated
	 * @param unknown $filePath
	 * @throws DirectoryNotFoundException
	 * @throws FileNotFoundException
	 * @return string
	 */
	public static function readAllText($filePath) {
		if(!file_exists($filePath)) { throw new DirectoryNotFoundException('디렉토리가 존재하지 않습니다.'); }
		elseif(!is_readable($filePath)) { throw new FileNotFoundException("파일({$filePath})을 찾을 수 없습니다."); }

		return file_get_contents($filePath);
	}
}