<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 3:25
 */

namespace classes\context;


use classes\exception\http\HTTPResponseException;

class RequestSession {
	use ContextAssistTrait;

	/** @var RequestContext */
	private $requestContext;
	private $options;

	/**
	 * RequestSession constructor.
	 * @param $requestContext
	 */
	public function __construct(RequestContext $requestContext) { $this->requestContext = $requestContext; }

	public function open($options = []) {
		if (!TEST && !$this->isOpened()) {
			$this->options = $options;
			session_start($options);
		} elseif($this->options != $options) {
			throw new \RuntimeException("Session options are different.");
		}
	}


	/**
	 * @return bool
	 */
	public function isOpened(): bool {
		if (php_sapi_name() !== 'cli') {
			if (version_compare(phpversion(), '5.4.0', '>=')) {
				return session_status() === PHP_SESSION_ACTIVE ? true : false;
			} else {
				return session_id() === '' ? false : true;
			}
		}

		return false;
	}

	public function getSessionId(): string {
		if (session_id() == '') throw new \RuntimeException ('you must be sesseion_start()');

		return session_id();
	}

	public function getValue(string $key, $defaultValue = null, bool $trim = false) {
		if ($trim) return array_key_exists($key, $_SESSION) ? $this->requestParamTrim($_SESSION [$key], $defaultValue) : $defaultValue;
		else return array_key_exists($key, $_SESSION) ? $_SESSION [$key] : $defaultValue;
	}

	public function setValue(string $key, &$val) {
		if ($this->isOpened()) {
			$_SESSION[$key] = $val;
			return;
		}

		throw new HTTPResponseException("Session not opened", 500);
	}

}