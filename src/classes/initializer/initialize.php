<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 5:08
 */

use classes\initializer\Initializer;

$initializerList = [
	'classes\initializer\TestRuntimeDetectorInitializer',
	'classes\initializer\EnvironmentInitializer',
	'classes\initializer\DirConstantsInitializer',
	'classes\initializer\ContextInitializer',
//	'classes\initializer\AppInitializerDelegate',
	'classes\initializer\ErrorHandlerInitializer',
	'classes\initializer\DispatcherInitializer',
];

foreach ($initializerList as $name) {
	/** @var Initializer $initializer */
	$class = $name;
	$initializer = new $class();
	$initializer->init();
}

