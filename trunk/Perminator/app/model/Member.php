<?php
namespace app\model;

class Member {
	private $id;
	private $name;
	private $password;

	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getPassword() {
		return $this->password;
	}
}

?>