<?php
namespace component\gnuboard\factory\pool;
use component\gnuboard\repository\MysqliMemberRepository;

class MemberRepositoryFactoryPool {
	/**
	 * @return \app\gnuboard\repository\IMemberRepository
	 */
	static function getInstance() {
		static $instance = null;
		if(is_null($instance))
			$instance = new MysqliMemberRepository();

		return $instance;
	}
}