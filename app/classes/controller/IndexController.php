<?php
namespace classes\controller;
use classes\ui\ModelMap;
use classes\model\Member;
use classes\stereotype\Controller;
use classes\web\script\ModelAndView;
use classes\context\Context;

/**
 * @author 경대
 *
 */
class IndexController implements Controller {
	private $context;

	public function index($no) {
		return "index.php";
	}

	public function indexPost(ModelMap $map ,$no) {
		$view = new ModelAndView("result.php");
		$map->addAttribute("no", $no);
		$view->setModelMap($map);
		$view->setContentType("text/plain");

		return $view;
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
