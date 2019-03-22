<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-04-01
 * 시간: 오후 10:48
 */

namespace classes\lang;


/**
 * Class StringPool
 *
 * @package classes\lang
 */
class StringPool extends UniqueObject {
	/** @var string[] */
	private $strings = array();

	/** @return StringPool */
	public static function getInstance() {
		static $instance = null;
		if ($instance == null)
			$instance = new self();

		return $instance;
	}

	/**
	 * getInstance 로 접근해라!
	 */
	private function __construct() { }

	/**
	 * @param string      $string
	 * @param int|string|IUniqueObject|null $key
	 * @return int
	 * @throw \InvalidArgumentException
	 */
	public function put($string, $key = null) {
		$key = $this->createKey($string, $key);
		$this->strings[$key] = $string;

		return $key;
	}

	/**
	 * @param $string
	 * @param $key
	 * @return int|string
	 */
	private function createKey($string, $key) {
		if($key !== null && is_scalar($key))
			return strval($key);
		elseif ($key == null && is_string($string))
			return $this->createInternalKey();
		elseif ($key == null && ($string instanceof IUniqueObject) && method_exists($string, '__toString')) {
			return $string->hashCode();
		} elseif ($key != null && $key instanceof IUniqueObject)
			return $key->hashCode();
		else {
			throw new \InvalidArgumentException("not supported type");
		}
	}

	/** @return int */
	private function createInternalKey() {
		$newKey = null;

		while (true) {
			$newKey = time();
			if (!array_key_exists($newKey, $this->strings)) break;
		}

		return $newKey;
	}

	/**
	 * @param int|string|IUniqueObject $key
	 * @param null|string   $default
	 * @return null|string
	 */
	public function get($key, $default = null) {
		$key = $this->resolveKey($key);
		return array_key_exists($this->resolveKey($key), $this->strings) ? $this->strings[$key] : $default;
	}

	/**
	 * @param int|string|IUniqueObject $key
	 * @return string
	 * @throw \InvalidArgumentException
	 */
	private function resolveKey($key) {
		if(is_scalar($key))
			return $key;
		elseif($key instanceof IUniqueObject)
			return $key->hashCode();
		else
			throw new \InvalidArgumentException("Not Supported Key Type");
	}

	/** @param int|string|IUniqueObject $key */
	public function remove($key) {
		$key = $this->resolveKey($key);
		if(array_key_exists($key, $this->strings))
			unset($this->strings[$key]);
	}

	/**
	 * @param int|string|IUniqueObject $key
	 * @return bool
	 * @throw \InvalidArgumentException
	 */
	public function containsKey($key): bool{
		/** @noinspection PhpParamsInspection */
		return array_key_exists($this->resolveKey($key), $this->strings);
	}

	/**
	 * @param int|string|IUniqueObject $string
	 * @return mixed
	 * @throw \InvalidArgumentException
	 */
	public function key($string) { return array_search($string, $this->strings); }

	/**
	 * @param int|string|IUniqueObject $key
	 */
	public function delete($key) {
		unset($this->strings[$this->resolveKey($key)]);
	}
}