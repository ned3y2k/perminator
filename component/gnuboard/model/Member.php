<?php
namespace component\gnuboard\model;

class Member {
	public $no;
	public $id;
	public $password;
	public $name;
	public $nick;
	public $nickDate;
	public $email;
	public $homepage;
	public $password_q;
	public $password_a;
	public $level;
	public $jumin;
	public $sex;
	public $birth;
	public $tel;
	public $hp;
	public $zip1;
	public $zip2;
	public $addr1;
	public $addr2;
	public $signature;
	public $recommend;
	public $point;
	public $todayLogin;
	public $loginIp;
	public $datetime;
	public $ip;
	public $leaveDate;
	public $interceptDate;
	public $emailCertify;
	public $memo;
	public $lost_certify;
	public $mailling;
	public $sms;
	public $open;
	public $openDate;
	public $profile;
	public $memoCall;
	public $mb1;
	public $mb2;
	public $mb3;
	public $mb4;
	public $mb5;
	public $mb6;
	public $mb7;
	public $mb8;
	public $mb9;
	public $mb10;

	public function isGroupAdmin() {
		// TODO 살려야함
		throw new \RuntimeException("Unimplemented Method");
		return $group['gr_admin'] == $this->id;
	}

	public function isBoardAdmin() {
		// TODO 살려야함
		throw new \RuntimeException("Unimplemented Method");
		return $board['bo_admin'] == $this->id;
	}

	public function isSuperAdmin() {
		// TODO 살려야함
		throw new \RuntimeException("Unimplemented Method");
		return $config['cf_admin'] == $this->id;
	}
}