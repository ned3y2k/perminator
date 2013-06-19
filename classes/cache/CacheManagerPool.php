<?php
namespace classes\cache;

use conf\Core;
class CacheManagerPool {
	const APC = '\classes\cache\APCCacheManager';
	const Serializing = '\classes\cache\SerializingCacheManager';
	const eAccelerator = '\classes\cache\eAcceleratorCacheManager';
	const WinCache = '\classes\cache\WinCacheCacheManager';

	/**
	 *
	 * @param string $cacheManagerClass
	 * @return CacheManager
	 */
	public static function getIntance($cacheManagerClass = null) {
		if(is_null($cacheManagerClass)) $cacheManagerClass = Core::CACHE_MANAGER_CLASS;
		static $cacheManagerMap = array ();
		return array_key_exists ( $cacheManagerClass, $cacheManagerMap ) ? $cacheManagerMap [$cacheManagerClass] : $cacheManagerMap [$cacheManagerClass] = new $cacheManagerClass ();
	}
}