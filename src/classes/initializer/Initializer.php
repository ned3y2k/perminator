<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 4:19
 */

namespace classes\initializer;

interface Initializer {
	public function init();
}

require_once 'initialize.php';