<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-05-15
 * 시간: 오전 4:25
 */

namespace classes\util;


/**
 * Class ThrowableLogger
 *
 * @package classes\util
 */
class ThrowableLogger {
	/** @var string Exception Log Dir */
	private $dir;

	/** @return ThrowableLogger */
	public static function getInstance() {
		static $instance = null;
		if ($instance == null)
			$instance = new self();

		return $instance;
	}

	function __construct() {
		$this->initLogDir();
	}

	private function initLogDir() {
		$dir = _DIR_LOG_PHP_EXCEPTION_ . date('Ymd') . DIRECTORY_SEPARATOR;
		if (!file_exists($dir))
			mkdir($dir, 7666, true);

		$this->dir = $dir;
	}

	/**
	 * @param \Throwable $exception
	 * @throws \classes\io\exception\DirectoryNotFoundException
	 * @throws \classes\io\exception\PermissionException
	 */
	public function writeObjectLog(\Throwable $exception) {
		DevUtil::varExport($this->createLogFileName($exception) . '.log', $exception, false);
	}

	/**
	 * @param \Throwable $throwable
	 *
	 * @return string
	 */
	private function createLogFileName(\Throwable $throwable) {
		return $this->dir . date('His') . '-' . trim(str_replace("\\", '.', get_class($throwable)), '.');
	}
}