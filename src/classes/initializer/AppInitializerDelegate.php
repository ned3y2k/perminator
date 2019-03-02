<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-10
 * Time: 오후 3:12
 */

namespace classes\initializer;

use app\classes\initializer\AppInitializer;

class AppInitializerDelegate implements Initializer {
	public function init() {
		$initializer = new AppInitializer();
		$initializer->init();
	}
}