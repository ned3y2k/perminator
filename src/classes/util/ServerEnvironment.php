<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-13
 * Time: 오전 7:31
 */

namespace classes\util;


use classes\context\IApplicationContext;

class ServerEnvironment {
	/**
	 * @var IApplicationContext
	 */
	private $applicationContext;


	/**
	 * ServerEnvironment constructor.
	 *
	 * @param IApplicationContext $applicationContext
	 */
	public function __construct(IApplicationContext $applicationContext) {
		$this->applicationContext = $applicationContext;
	}

	public function getOSName() { return php_uname('s'); }

	public function getReleaseName() { return php_uname('r'); }

	public function getVersion() { return php_uname('v'); }

	public function getMachine() { return php_uname('m'); }

	public function osIsWindows() { return strpos(strtolower(php_uname('s')), 'windows') !== false; }

	public function oswIsLinux() { return strpos(strtolower(php_uname('s')), 'linux') !== false; }

	public function isForceHttp() {
		$debugContext = $this->applicationContext->getDebugContext();
		return $debugContext->available() && $debugContext->getAsBoolean('forceHttp');
	}
}