<?php
namespace app\controller;
use classes\web\script\ModelAndView;
use app\model\Member;
use classes\web\bind\meta\RequestParam;
use classes\web\bind\meta\RequestParamCollection;
use classes\meta\Controller;

class MemberRequestParamCollection implements RequestParamCollection {
	private $paramMap = array ();
	public function __construct() {
		$this->paramMap ["member"] = new RequestParam ( 'app\model\Member', RequestParam::METHOD_GET, true );
	}
	public function &getRequestParams() {
		return $this->paramMap;
	}

	/**
	 *
	 * @return Member
	 */
	public function getMember() {
		return $this->paramMap ["member"]->value;
	}

	/*
	 * (non-PHPdoc) @see
	 * \classes\web\bind\meta\RequestParamCollection::getKeyNamePrefix()
	 */
	public function getKeyNamePrefix() {
		return "";
	}
}
class IndexController implements Controller {
	public function index() {
		return "index.php";
	}
	public function saveMember(MemberRequestParamCollection $paramColl) {
		$member = $paramColl->getMember ();

		$view = new ModelAndView ( "memberShow.php" );
		$modelMap ["member"] = $paramColl->getMember ();
		$view->setModelMap ( $modelMap );
		return $view;
	}
}

?>