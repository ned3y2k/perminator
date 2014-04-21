<?php
namespace classes\io\scanner;

use classes\io\File;
interface IRecursiveScanner {
	/**
	 * @param unknown $rootPath
	 * @return string[] file path list
	 */
	public function scan($rootPath);

	/**
	 * @param RecursiveFileFilter $filter
	 */
	public function prepare(RecursiveFileFilter $filter);
}

interface IRecursiveFilter {
	public function conformityConditions($compareValue);
}

class RecursiveFileFilter implements IRecursiveFilter {
	private static $contain = '-1';
	private static $extName = '1';
	private static $regex = '2';

	public static function createContainFilter($value) { return new self($value, self::$contain); }
	public static function createExtNameFilter($value) { return new self($value, self::$extName); }
	public static function createRegExFilter($value) { return new self($value, self::$regex); }

	private $mode;
	private $value;

	private function __construct($conditionValue, $mode) {
		// FIXME case-insensitive 필요할까??
		$this->value = self::$regex == $mode ? $conditionValue : strtolower($conditionValue);
		$this->mode = $mode;
	}

	/**
	 * @param boolean $value
	 */
	public function conformityConditions($compareValue) {
		switch ($this->mode) {
			case self::$contain:
				return strpos($compareValue, $this->value) !== false;
			case self::$extName:
				return strtolower(File::getFileExtName($compareValue)) == $this->value;
			case self::$regex:
				return preg_match($this->value, $compareValue);
		}
	}
}

class RecursiveFileScanner implements IRecursiveScanner {
	/**
	 * @var RecursiveFileFilter
	 */
	private $filter;
	private $foundFiles = array();

	public function prepare(RecursiveFileFilter $filter) {
		$this->filter = $filter;
	}

	public function scan($rootPathName) {
		if(is_null($this->filter)) throw new \LogicException("not prepared RecursiveFileFilter.");

		if($rootPathName instanceof \SplFileInfo){
			/* @var $rootPathName \SplFileInfo */
			$dirs = new \RecursiveDirectoryIterator($rootPathName->getPathname());
		} elseif(is_string($rootPathName)) {
			/* @var $rootPathName string */
			$dirs = new \RecursiveDirectoryIterator($rootPathName);
		}

		if(isset($dirs))
		foreach ($dirs as $file) {
			/* @var $file \SplFileInfo */
			$fileName = $file->getFilename();
			$pathName = realpath($file->getPathname());

			if($fileName == '..' || $fileName == '.') continue;
			elseif(is_dir($pathName)) {
				$this->scan($pathName);
				continue;
			}

			if($this->filter->conformityConditions($pathName)) {
				$this->foundFiles[] = $pathName;
			}
		}

		return $this->foundFiles;
	}
}

class RecursiveDirectoryScanner implements IRecursiveScanner {
	/**
	 * @var RecursiveFileFilter
	 */
	private $filter;
	private $foundFiles = array();

	public function prepare(RecursiveFileFilter $filter) {
		$this->filter = $filter;
	}

	public function scan($rootPathName) {
		if(is_null($this->filter)) throw new \LogicException("not prepared RecursiveFileFilter.");

		if($rootPathName instanceof \SplFileInfo){
			/* @var $rootPathName \SplFileInfo */
			$dirs = new \RecursiveDirectoryIterator($rootPathName->getPathname());
		} elseif(is_string($rootPathName)) {
			/* @var $rootPathName string */
			$dirs = new \RecursiveDirectoryIterator($rootPathName);
		}

		foreach ($dirs as $dir) {
			/* @var $file \SplFileInfo */
			$fileName = $dir->getFilename();
			$pathName = realpath($dir->getPathname());

			if($fileName == '..' || $fileName == '.') continue;
			elseif(is_dir($pathName)) {
				if($this->filter->conformityConditions($pathName))
					$this->foundFiles[] = $pathName;
				$this->scan($pathName);
			}
		}

		return $this->foundFiles;
	}
}