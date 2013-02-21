<?php
namespace component\gnuboard\repository;
use component\gnuboard\pool\GNUConfigPool;
use component\gnuboard\model\Member;
use conf\GNUConfig;

class MysqliMemberRepository extends MysqliRepository implements IMemberRepository {
	private $table;
	function __construct() {
		parent::__construct();
		$gnuConfig = new GNUConfig ();
		$this->table = GNUConfigPool::getConfig()->member_table;
	}
	function selectById($userId) {
		$stmt = $this->connection->prepare ( "SELECT `mb_no`,`mb_id`,`mb_password`,`mb_name`, `mb_nick`,`mb_nick_date`,`mb_email`,`mb_homepage`,`mb_password_q`,`mb_password_a`,`mb_level`,`mb_jumin`,`mb_sex`,`mb_birth`,`mb_tel`,`mb_hp`,`mb_zip1`,`mb_zip2`,`mb_addr1`,`mb_addr2`,`mb_signature`,`mb_recommend`,`mb_point`,`mb_today_login`,`mb_login_ip`,`mb_datetime`,`mb_ip`,`mb_leave_date`,`mb_intercept_date`,`mb_email_certify`,`mb_memo`,`mb_lost_certify`,`mb_mailling`,`mb_sms`,`mb_open`,`mb_open_date`,`mb_profile`,`mb_memo_call`,`mb_1`,`mb_2`,`mb_3`,`mb_4`,`mb_5`,`mb_6`,`mb_7`,`mb_8`,`mb_9`,`mb_10` FROM {$this->table} WHERE `mb_id` = TRIM(?)" );
		$stmt->bind_param('s', $userId);
		$stmt->execute();

		$member = new Member();
		$stmt->bind_result ( $member->no, $member->id, $member->password, $member->name, $member->nick, $member->nickDate, $member->email, $member->homepage, $member->password_q, $member->password_a, $member->level, $member->jumin, $member->sex, $member->birth, $member->tel, $member->hp, $member->zip1, $member->zip2, $member->addr1, $member->addr2, $member->signature, $member->recommend, $member->point, $member->todayLogin, $member->loginIp, $member->datetime, $member->ip, $member->leaveDate, $member->interceptDate, $member->emailCertify, $member->memo, $member->lost_certify, $member->mailling, $member->sms, $member->open, $member->openDate, $member->profile, $member->memoCall, $member->mb1, $member->mb2, $member->mb3, $member->mb4, $member->mb5, $member->mb6, $member->mb7, $member->mb8, $member->mb9, $member->mb10 );
		$stmt->fetch();
		$stmt->free_result();
		$stmt->close();

		return $member;
	}
}