<?php
namespace classes\model\board;

class Post {
	private $id;

	function __construct() {
		$this->id = 1;
	}

	public function getId() {
		return $this->id;
	}
}