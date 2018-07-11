<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:29
 */

namespace classes\context;


class UserSessionContext {
	public function get($key, $defaultValue = null) {
		if (!is_scalar($key)) throw new \InvalidArgumentException("invalid key type");

		return array_key_exists($key, $_SESSION) ? $_SESSION [$key] : $defaultValue;
	}

	public function isStarted() {
		if (php_sapi_name() !== 'cli') {
			if (version_compare(phpversion(), '5.4.0', '>=')) {
				return session_status() === PHP_SESSION_ACTIVE ? true : false;
			} else {
				return session_id() === '' ? false : true;
			}
		}

		return false;
	}

	public function getSessionId() {
		if (session_id() == '') throw new \RuntimeException ('you must be sesseion_start()');

		return session_id();
	}
}