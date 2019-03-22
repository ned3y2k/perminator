<?php

namespace classes\lang;

use classes\io\Directory;
use classes\io\exception\FileNotFoundException;
use classes\io\exception\PermissionException;
use classes\io\File;

/**
 * @author 경대
 * Perminator Class
 */
class FileStringBuilder extends UniqueObject implements IStringBuilder {
	/** @var string 임시 저장 경로 */
	private $dir;
	/** @var mixed 생성 시간 */
	private $time;
	/** @var string 임시 저장 파일명 */
	private $tmpFileName;

	/** 가끔 소멸자에서 지우지 못하는 내용이 발생하는데 그놈들을 청소하는 제한 시간이다 */
	const LIMIT = 60;

	/**
	 * FileStringBuilder constructor.
	 * @throws PermissionException
	 * @throws FileNotFoundException
	 */
	public function __construct() {
		$this->dir = _DIR_VAR_ . 'tmp' . DIRECTORY_SEPARATOR . 'FileStringBuilder'.DIRECTORY_SEPARATOR;
		if(!Directory::exist($this->dir))
			Directory::create($this->dir);

		$this->time = microtime(true);
		$this->clean();

        $this->tmpFileName = $this->dir . $this->time;

	}

	/**
	 * @throws FileNotFoundException
	 */
	function __destruct() {
		File::delete($this->tmpFileName);
	}

	/**
	 * @param string $str
	 *
	 * @return IStringBuilder
	 * @throws \classes\io\exception\DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function append($str) {
		if (!is_string($str) && $str instanceof self) throw new \InvalidArgumentException();
		File::appendAllText($this->tmpFileName, $str);
		StringPool::getInstance()->delete($this);
		return $this;
	}

	/**
	 * @return string
	 * @throws FileNotFoundException
	 */
	public function toString(): string {
		$string = StringPool::getInstance()->get($this);
		if($string == null) {
			$string = File::readAllLine($this->tmpFileName);
			StringPool::getInstance()->put($string, $this);
		}

		return $string;
	}

	/** @return string
	 * @throws FileNotFoundException
	 */
	public function __toString(): string {
		return $this->toString();
	}

	/**
	 * @throws FileNotFoundException
	 */
	private function clean() {
		$dirList = new \DirectoryIterator($this->dir);
		foreach($dirList as $file) {
			/** @var $file \DirectoryIterator */
			if($file->isFile() && floatval($file->getFilename()) + self::LIMIT < ($this->time)) {
				File::delete($file->getPathname());
			}
		}
	}
}