<?php
namespace classes\controller;
use classes\ui\ModelMap;
use classes\model\Member;
use classes\stereotype\Controller;
use classes\web\script\ModelAndView;
use classes\context\Context;
use classes\stereotype\AutowiredBeansOwnedController;
use classes\stereotype\RequestMapOwnedController;

/**
 * @author 경대
 *
 */
class IndexController implements Controller, AutowiredBeansOwnedController, RequestMapOwnedController {
	private $context;

	public function index(ModelMap $map, $no) {
		$view = new ModelAndView("index.php");
		$map->addAttribute("no", $no);
		$view->setModelMap($map);

		return $view;
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

	public static function createRequestMap(\classes\context\Context $context) {
	}

	public static function createAutowiredBeanMap(\classes\context\Context $context) {

	}
}
