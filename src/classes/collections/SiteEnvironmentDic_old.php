<?php
namespace classes\collections;

use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\FileNotFoundException;
use classes\io\exception\PermissionException;
use classes\io\File;
use classes\model\environment\SiteEnvironment;

class SiteEnvironmentDic_old {
	private $dictionary = array();

	/**
	 * @return self
	 * @throws FileNotFoundException
	 */
	public static function getInstance() {
		static $instance = null;

		return $instance == null ? new self() : $instance;
	}

	/**
	 * SiteEnvironmentDic_old constructor.
	 * @throws FileNotFoundException
	 */
	private function __construct() {
		$filePath = _DIR_SERIALIZE_CONF_ . 'SiteEnvironmentDic.php';
		if (file_exists($filePath)) $this->dictionary = unserialize(File::readAllLine($filePath));
	}

	/**
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function __destruct() {
		$dir = substr(_DIR_SERIALIZE_CONF_, 0, -1);
		if (!file_exists($dir)) mkdir($dir, 7666);

		$filePath = _DIR_SERIALIZE_CONF_ . 'SiteEnvironmentDic.php';
		File::writeAllText($filePath, serialize($this->dictionary));

		unset($this->dictionary);
	}

	public function get($name) {
		if (array_key_exists($name, $this->dictionary)) return $this->dictionary[ $name ];

		return new SiteEnvironment();
	}

	public function put(SiteEnvironment $env) {
		$this->dictionary[ $env->name ] = $env;
	}
}