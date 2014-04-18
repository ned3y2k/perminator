<?php
namespace classes\cache;


class SerializingCacheManager implements CacheManager {
	private $cacheDir;

	public function __construct() {
		$this->initSerializeCacheDir(initCacheDir());
	}

	/* (non-PHPdoc)
	 * @see \classes\cache\CacheManager::put()
	 */
	public function put($key, $instance, $expire = 0) {
		if(!(file_exists($key) && filemtime($key) <= time() + $expire)) {
			if(file_exists($key)) $this->delete($key);
			$dir = $this->initDirStruct($key);
			file_put_contents($this->cacheDir.DIRECTORY_SEPARATOR.$key, serialize($instance));
		}
	}

	/* (non-PHPdoc)
	 * @see \classes\cache\CacheManager::get()
	 */
	public function get($key) {
		$fileName = $this->cacheDir.DIRECTORY_SEPARATOR.$key;

		if(file_exists($fileName)) return unserialize($fileName);
		else return null;
	}

	/* (non-PHPdoc)
	 * @see \classes\cache\CacheManager::delete()
	 */
	public function delete($key) {
		unlink($this->cacheDir.DIRECTORY_SEPARATOR.$key);
	}

	/* (non-PHPdoc)
	 * @see \classes\cache\CacheManager::clear()
	 */
	public function clear() {
		unlink_recursive($this->cacheDir);
	}

	private function initSerializeCacheDir($rootCacheDir) {
		$cacheDir = $rootCacheDir.DIRECTORY_SEPARATOR."serialize";
		if(!file_exists($cacheDir)) mkdir($cacheDir);
		$this->cacheDir = $cacheDir;
	}

	private function initDirStruct($key) {
		$keyName = str_replace('\\', DIRECTORY_SEPARATOR, $key);
		$dirs = explode(DIRECTORY_SEPARATOR, dirname($keyName));

		$currentDir = $this->cacheDir;
		foreach ($dirs as $dir) {
			$currentDir .= DIRECTORY_SEPARATOR.$dir;
			if(!file_exists($currentDir)) mkdir($currentDir);
		}
	}
}