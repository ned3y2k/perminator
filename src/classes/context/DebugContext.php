<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-08-16
 * Time: 오후 11:18
 */

namespace classes\context;

use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\io\File;
use classes\lang\ArrayUtil;

class DebugContext {
	private $available;
	private $flags;

	public function __construct(string $debugFilePath) {
		if (file_exists($debugFilePath)) {
			$this->available = true;
			$this->flags     = parse_ini_file(_APP_ROOT_ . 'debug');
		}
	}

	public function available(): bool {
		return $this->available ? $this->available : false;
	}

	/**
	 * @param string|null $key
	 * @param string $default 값이 지정되어 있지 않을때 기본 값
	 *
	 * @return array|string
	 */
	public function get($key = null, $default = null) {
		if ($key == null) return $this->flags;

		return ArrayUtil::getValue($this->flags, $key, $default);
	}

	public function getAsBoolean($key = null, $default = null) {
		$val = $this->get($key, null);

		if($val === null)
			return $default;

		if($val == 0 || $val == false)
			return false;


		return true;
	}

	/**
	 * 디버그 정보 입력
	 *
	 * @param string $fileName 파일경로
	 * @param object|mixed $object 기록할 내용
	 *
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function writeDebugInfo($fileName, $object) {
		File::appendAllText(_DIR_LOG_PHP_USR_ . $fileName, var_export($object, true), true);
	}
}