<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-10
 * Time: 오후 3:12
 */

namespace classes\initializer;

class AppInitializerDelegate implements Initializer {
	public function init() {
		/** @var $loader \Composer\Autoload\ClassLoader */
		$loader = spl_autoload_functions()[0][0];

		$appInitializer = 'app\classes\initializer\AppInitializer';
		if($loader->findFile($appInitializer)) {
			/** @var Initializer $initializer */
			$initializer = new $appInitializer();
			$initializer->init();
		} else {
			echo $appInitializer . " Not Found. Please use a init Command.\n";
		}

	}
}