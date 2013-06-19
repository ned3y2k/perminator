<?php
namespace classes\cache;

class APCCacheManager implements CacheManager {
	public function __construct() {
		unsupported_operation();
	}

	/* (non-PHPdoc)
	 * @see \classes\cache\CacheManager::put()
	 */
	public function put($key, $instance, $expire = 0) {
		apc_add($key, $instance, $expire);
	}

	/* (non-PHPdoc)
	 * @see \classes\cache\CacheManager::get()
	 */
	public function get($key) {
		return apc_fetch($key);
	}

	/* (non-PHPdoc)
	 * @see \classes\cache\CacheManager::delete()
	 */
	public function delete($key) {
		apc_delete($key);

	}

	/* (non-PHPdoc)
	 * @see \classes\cache\CacheManager::clear()
	 */
	public function clear() {
		apc_clear_cache();
	}

}