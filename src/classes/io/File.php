<?php
namespace classes\io;

use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\FileNotFoundException;
use classes\io\exception\IOException;
use classes\io\exception\PermissionException;

class File {
	const CODE_EXIST_FILE = 1;

	/**
	 * @param $sourceFilePath
	 * @param $destFilePath
	 * @param bool $overwrite
	 * @throws DirectoryNotFoundException
	 * @throws FileNotFoundException
	 * @throws IOException
	 * @throws PermissionException
	 */
	public static function copy($sourceFilePath, $destFilePath, $overwrite = true) {
		$sourceFilePath = create_path($sourceFilePath);
		$destFilePath   = create_path($destFilePath);

		if ($overwrite && file_exists($destFilePath)) {
			throw new IOException('대상 파일이 이미 존재합니다.', self::CODE_EXIST_FILE);
		} elseif (!file_exists(dirname($destFilePath))) {
			throw new DirectoryNotFoundException('대상 디렉토리가 존재하지 않습니다.');
		} elseif (!file_exists($sourceFilePath)) {
			throw new FileNotFoundException("소스 파일({$sourceFilePath})을 찾을 수 없습니다.");
		} elseif (!@is_readable($sourceFilePath)) {
			throw new PermissionException('원본 파일을 읽을수 없습니다.');
		} elseif (!@is_writable($destFilePath)) {
			throw new PermissionException('대상 파일을 기록할수 없습니다.');
		}

		if (!copy($sourceFilePath, $destFilePath)) {
			throw new IOException('알수 없는 에러', IOException::CODE_UNKNOWN);
		}
	}

	/**
	 * @param $sourceFilePath
	 * @param $destFilePath
	 * @throws DirectoryNotFoundException
	 * @throws FileNotFoundException
	 * @throws IOException
	 * @throws PermissionException
	 */
	public static function move($sourceFilePath, $destFilePath) {
		$sourceFilePath = create_path($sourceFilePath);
		$destFilePath   = create_path($destFilePath);

		if (file_exists($destFilePath)) {
			throw new IOException('대상 파일이 이미 존재합니다.', self::CODE_EXIST_FILE);
		} elseif (!file_exists(dirname($destFilePath))) {
			throw new DirectoryNotFoundException('대상 디렉토리가 존재하지 않습니다.');
		} elseif (!file_exists($sourceFilePath)) {
			throw new FileNotFoundException("소스 파일({$sourceFilePath})을 찾을 수 없습니다.");
		} elseif (!@is_readable($sourceFilePath)) {
			throw new PermissionException('원본 파일을 읽을수 없습니다.');
		} elseif (!@dirname($destFilePath)) {
			throw new PermissionException('대상 파일을 기록할수 없습니다.');
		}

		if (!rename($sourceFilePath, $destFilePath)) {
			throw new IOException('알수 없는 에러', IOException::CODE_UNKNOWN);
		}
	}


	/**
	 * 파일에 내용을 추가한다
	 *
	 * @param string $filePath
	 * @param string $content
	 * @param        $lock
	 * @param        $fileUseIncludePath
	 *
	 * @throws \InvalidArgumentException
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public static function appendAllText($filePath, $content, $lock = false, $fileUseIncludePath = false) {
		$filePath = create_path($filePath);

		if (!is_scalar($content)) {
			throw new \InvalidArgumentException('content is an integer, floating point, string, or Boolean only');
		} elseif (!file_exists($dir = dirname($filePath))) {
			throw new DirectoryNotFoundException($dir.' Directory is not exists');
		} elseif (file_exists($filePath) && !@is_writable($filePath)) {
			throw new PermissionException("{$filePath} 파일을 기록할수 없습니다.");
		}

		$flag = FILE_APPEND;
		if ($lock == true)
			$flag = LOCK_EX;
		if ($fileUseIncludePath == true)
			$flag = $flag | FILE_USE_INCLUDE_PATH;

		file_put_contents($filePath, $content, $flag);
	}

	/**
	 * 기존 파일에 내용을 덮어쓴다.
	 *
	 * @param string $filePath
	 * @param string $content
	 * @param bool   $lock
	 * @param bool   $fileUseIncludePath
	 *
	 * @throws exception\PermissionException
	 * @throws \InvalidArgumentException
	 * @throws exception\DirectoryNotFoundException
	 */
	public static function writeAllText($filePath, $content, $lock = false, $fileUseIncludePath = false) {
		$filePath = create_path($filePath);

		if (!is_scalar($content)) {
			throw new \InvalidArgumentException('content is an integer, floating point, string, or Boolean only');
		} elseif (!file_exists(dirname($filePath))) {
			throw new DirectoryNotFoundException('디렉토리가 존재하지 않습니다.');
		} elseif (file_exists($filePath) && !@is_writable($filePath)) {
			throw new PermissionException("{$filePath} 파일을 기록할수 없습니다.");
		}

		$flag = 0;
		if ($lock == true)
			$flag = LOCK_EX;
		if ($fileUseIncludePath == true)
			$flag = $flag | FILE_USE_INCLUDE_PATH;

		file_put_contents($filePath, $content, $flag);
	}

	/**
	 * @param string $path
	 * @param bool   $use_include_path
	 *
	 * @return string
	 * @throws exception\FileNotFoundException
	 */
	public static function readAllLine($path, $use_include_path = false) {
		$path = create_path($path);

		if (!self::exists($path, $use_include_path)) {
			throw new FileNotFoundException("파일({$path})을 찾을 수 없습니다.");
		} elseif (!@is_readable($path) && !$use_include_path) {
			throw new FileNotFoundException("파일({$path})을 읽을 수 없습니다.");
		}

		return file_get_contents($path, $use_include_path ? FILE_USE_INCLUDE_PATH : 0);
	}

	/**
	 * @deprecated
	 *
	 * @param string $filePath
	 *
	 * @throws DirectoryNotFoundException
	 * @throws FileNotFoundException
	 * @return string
	 */
	public static function readAllText($filePath) {
		$filePath = create_path($filePath);

		if (!file_exists($filePath)) {
			throw new DirectoryNotFoundException('디렉토리가 존재하지 않습니다.');
		} elseif (!is_readable($filePath)) {
			throw new FileNotFoundException("파일({$filePath})을 찾을 수 없습니다.");
		}

		return file_get_contents($filePath);
	}

	/**
	 * @param $filePath
	 * @param bool $use_include_path
	 * @param string $returnPath
	 * @return bool
	 */
	public static function exists($filePath, $use_include_path = false, &$returnPath = null) {
		if($use_include_path) {
			$paths = explode(PATH_SEPARATOR, ini_get('include_path'));

			foreach($paths as $path) {
				$path = normalizepath($path) . DIRECTORY_SEPARATOR.$filePath;

				if(file_exists($path)) {
					if($returnPath === null) $returnPath = $path;

					return true;
				}
			}

			return false;
		}

		return file_exists(create_path($filePath));
	}

	/**
	 * @param $path
	 * @throws FileNotFoundException
	 */
	public static function delete($path) {
		$path = create_path($path);

		if (!file_exists($path)) {
			throw new FileNotFoundException("파일({$path})을 찾을 수 없습니다.");
		} elseif (!@is_readable($path)) {
			throw new FileNotFoundException("파일({$path})을 찾을 수 없습니다.");
		}

		unlink($path);
	}

	/**
	 * @param $file
	 * @param bool $useIncludePath
	 * @return bool|int
	 * @throws FileNotFoundException
	 */
	public static function getModificationTime($file, $useIncludePath = false) {
		if(file_exists($file)) {
			return filemtime($file);
		}  else if($useIncludePath && self::exists($file, true, $returnPath)) {
			return filemtime($returnPath);
		} else {
			throw new FileNotFoundException($file);
		}
	}
}