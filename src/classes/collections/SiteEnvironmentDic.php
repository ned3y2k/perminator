<?php
namespace classes\collections;

use classes\io\Directory;
use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\io\File;

/**
 * Class SiteEnvironmentDic
 *
 * @package classes\collections
 */
class SiteEnvironmentDic implements IDictionary {
	/** @var array */
	private $dic = array();

	/** @throws \classes\io\exception\FileNotFoundException */
	public function __construct() {
		$filePath = _DIR_SERIALIZE_CONF_ . 'SiteEnvironmentDic.php';
		if (file_exists($filePath)) $this->dic = unserialize(File::readAllLine($filePath));
	}

	public function __destruct() { unset($this->dic); }

	/**
	 * @param string $key
	 * @param mixed $value
	 * @return mixed
	 */
	public function __set($key, $value) {
		if(property_exists($this, $key)) return $this->$key = $value;

		$this->dic[ $key ] = $value;
		return $value;
	}

	/**
	 * @param string $key
	 * @return mixed|null
	 */
	public function __get($key) {
		if(property_exists($this, $key)) return $this->$key;

		if (is_array($this->dic))
			return array_key_exists($key, $this->dic) ? $this->dic[ $key ] : null;

		return null;
	}

	/**
	 * @param string $key
	 * @return bool
	 */
	public function __isset($key) {
		if(property_exists($this, $key)) { return true; }
		return array_key_exists($key, $this->dic);
	}

	/** @param string $key */
	public function __unset($key) {
		if(property_exists($this, $key)) unset($this->$key);
		if (array_key_exists($key, $this->dic)) unset($this->dic[ $key ]);
	}

	/**
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public function save() {
		$dir = substr(_DIR_SERIALIZE_CONF_, 0, -1);
		if (!file_exists($dir)) Directory::create($dir);

		$filePath = _DIR_SERIALIZE_CONF_ . 'SiteEnvironmentDic.php';
		File::writeAllText($filePath, serialize($this->dic));
	}
}