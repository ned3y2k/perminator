<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:28
 */

namespace classes\context;


use classes\lang\ArrayUtil;

class SharedUserContext {
	private $requestedTime;
	private $sharedValues = array();

	public function __construct(\DateTime $requestedTime) {
		$this->requestedTime = $requestedTime;
	}

	/** @return \DateTime */
	public function getRequestedTime() { return $this->requestedTime; }

	/**
	 * @param string $key
	 * @param mixed  $value
	 */
	public function putSharedValue($key, $value) {
		if (array_key_exists($key, $this->sharedValues)) unset($this->sharedValues[$key]);
		$this->sharedValues[$key] = $value;
	}

	/**
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function getSharedValue($key) { return ArrayUtil::getValue($this->sharedValues, $key); }

	public function removeSharedValue($key) { if (array_key_exists($key, $this->sharedValues)) unset($this->sharedValues[$key]); }
}