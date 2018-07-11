<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 7. 25
 * 시간: 오후 2:05
 */

namespace classes\util\object;

class LoadedCacheList {
	private $files = array();

	/** @return self */
	public static function getInstance() {
		static $instance = null;

		return $instance == null ? $instance = new self() : $instance;
	}

	private function __construct() { }

	public function push($filePath) {
		$this->files[] = $filePath;
	}

	public function getFiles() {
		return $this->files;
	}

	public function loadAll() {
		foreach ($this->files as $file) {
			/** @noinspection PhpIncludeInspection */
			require_once create_path($file);
		}
	}
}
