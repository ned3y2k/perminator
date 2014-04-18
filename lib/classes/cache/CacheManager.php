<?php
namespace classes\cache;

interface CacheManager {
	public function put($key, $instance, $expire = 0);
	public function get($key);
	public function delete($key);
	public function clear();
}

