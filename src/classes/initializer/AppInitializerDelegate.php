<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-10
 * Time: 오후 3:12
 */

namespace classes\initializer;

use app\classes\initializer\AppInitializer;

require_once _APP_ROOT_.'app/classes/initializer/AppInitializer.php';

class AppInitializerDelegate implements Initializer {
	public function init() {
		$initializer = new AppInitializer();
		$initializer->init();
	}
}