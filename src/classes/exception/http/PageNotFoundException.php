<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-10
 * Time: 오후 5:45
 */

namespace classes\exception\http;


class PageNotFoundException extends HttpResponseException {

	/**
	 * PageNotFoundException constructor.
	 */
	public function __construct() {
		parent::__construct('Page not Found', 404);
	}
}