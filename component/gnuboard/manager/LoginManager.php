<?php
namespace component\gnuboard\manager;
use component\gnuboard\factory\pool\MemberRepositoryFactoryPool;
use component\gnuboard\pool\GNUConfigPool;

class LoginManager {
	/**
	 * @var string
	 */
	private $sessionUserId;
	private $repo;

	function __construct() {
		$this->repo = MemberRepositoryFactoryPool::getInstance();
		$this->initEnvironment ();
	}

	private function initEnvironment() {
	 	global $SESSION_CACHE_LIMITER;
		session_save_path("gnuboard4/data/session");

		ini_set("session.cache_expire", 180); // 세션 캐쉬 보관시간 (분)
		ini_set("session.gc_maxlifetime", 10800); // session data의 garbage collection 존재 기간을 지정 (초)
		ini_set("session.gc_probability", 1); // session.gc_probability는 session.gc_divisor와 연계하여 gc(쓰레기 수거) 루틴의 시작 확률을 관리합니다. 기본값은 1입니다. 자세한 내용은 session.gc_divisor를 참고하십시오.
		ini_set("session.gc_divisor", 100); // session.gc_divisor는 session.gc_probability와 결합하여 각 세션 초기화 시에 gc(쓰레기 수거) 프로세스를 시작할 확률을 정의합니다. 확률은 gc_probability/gc_divisor를 사용하여 계산합니다. 즉, 1/100은 각 요청시에 GC 프로세스를 시작할 확률이 1%입니다. session.gc_divisor의 기본값은 100입니다.

		session_set_cookie_params(0, "/");
		ini_set("session.cookie_domain", GNUConfigPool::getConfig()->cookie_domain);

		if (isset($SESSION_CACHE_LIMITER))
			@session_cache_limiter($SESSION_CACHE_LIMITER);
		else
			@session_cache_limiter("no-cache, must-revalidate");

		session_start();

		$this->sessionUserId = $_SESSION['ss_mb_id'];
	}


	function isLogin() {
		return array_key_exists($_SESSION, 'ss_mb_id');
	}

	function getMember() {
		return $this->repo->selectById($this->sessionUserId);
	}
}