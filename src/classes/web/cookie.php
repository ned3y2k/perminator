<?php
namespace classes\web\cookie;

interface ICookieContainer {
	function addCookie(Cookie $cookie);
	function remove($key);
}

class CookieContainer implements ICookieContainer {
	function addCookie(Cookie $cookie) {
		setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpireTimestamp(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
	}

	public function remove($key) {
		unset($_COOKIE[$key]);
		setcookie($key, null, -1, '/');
	}
}

class RawCookieContainer implements ICookieContainer {
	function addCookie(Cookie $cookie) {
		setrawcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpireTimestamp(), $cookie->getPath(), $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly());
	}

	public function remove($key) {
		unset($_COOKIE[$key]);
		setcookie($key, null, -1, '/');
	}
}

class Cookie {
	/**
	 * @var \DateTime
	 */
	private $expire;
	private $domain;
	private $path;
	private $httpOnly;
	private $secure;
	private $name;
	private $value;

	public function __construct($name, $value = '', \DateTime $expire = null, $path = '', $domain = '', $secure = false, $httpOnly = false) {
		$this->setName($name);
		$this->setValue($value);
		$this->setExpire($expire);
		$this->setPath($path);
		$this->setDomain($domain);
		$this->setSecure($secure);
		$this->setHttpOnly($httpOnly);
	}

	public function setRawCookie() {
		setrawcookie($this->name, $this->value, $this->expire != null ? $this->expire->getTimestamp() : 0, $this->path, $this->domain, $this->secure, $this->httpOnly);
	}

	public function getValue() {
		return $this->value;
	}

	public function getName() {
		return $this->name;
	}

	public function getDomain() {
		return $this->domain;
	}

	/**
	 * @return \DateTime
	 */
	public function getExpire() {
		return $this->expire;
	}

	public function getExpireTimestamp() {
		return $this->expire == null ? 0 : $this->expire;
	}

	public function getPath() {
		return $this->path;
	}

	public function isHttpOnly() {
		return $this->httpOnly;
	}


	public function isSecure() {
		return $this->secure;
	}

	/**
	 * @param string $domain
	 * @throws \InvalidArgumentException
	 */
	public function setDomain($domain) {
		if(!is_string($domain)) throw new \InvalidArgumentException();
		$this->domain = $domain;
	}

	/**
	 * @param \DateTime $expire
	 */
	public function setExpire(\DateTime $expire = null) {
		$this->expire = $expire;
	}

	/**
	 * @param string $path
	 * @throws \InvalidArgumentException
	 */
	public function setPath($path) {
		if(!is_string($path)) throw new \InvalidArgumentException();
		$this->path = $path;
	}

	/**
	 *
	 * @param bool $httpOnly
	 * @throws \InvalidArgumentException
	 */
	public function setHttpOnly($httpOnly) {
		if(!is_bool($httpOnly)) throw new \InvalidArgumentException();
		$this->httpOnly = $httpOnly;
	}

	/**
	 * @param bool $secure
	 * @throws \InvalidArgumentException
	 */
	public function setSecure($secure) {
		if(!is_bool($secure)) throw new \InvalidArgumentException();
		$this->secure = $secure;
	}

	/**
	 *
	 * @param string $name
	 * @throws \InvalidArgumentException
	 */
	public function setName($name) {
		if(!is_string($name)) throw new \InvalidArgumentException();
		$this->name = $name;
	}

	/**
	 *
	 * @param string $value
	 * @throws \InvalidArgumentException
	 */
	public function setValue($value) {
		if(!is_string($value)) throw new \InvalidArgumentException();
		$this->value = $value;
	}
}