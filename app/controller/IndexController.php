<?php
namespace app\controller;
use classes\ui\ModelMap;
use app\model\Member;
use classes\stereotype\Controller;
use classes\content\Context;

/**
 * @author 경대
 *
 */
class IndexController implements Controller {
	private $context;

	public function index($no) {
		// return "index.php";
	}

	public function indexPost($no) {
		echo $no;
	}

	public function saveMember(ModelMap $map, $id = null, $name = null,
			$password = null) {
		$member = new Member($id, $name, $password);
		$map->addAttribute('member', $member);
		return "memberShow.php";
	}

	public function setContext(Context $context) {
		$this->context = $context;
	}
}
