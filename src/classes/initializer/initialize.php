<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 5:08
 */

use classes\initializer\Initializer;

$initializerList = [
	'TestRuntimeDetectorInitializer',
	'EnvironmentInitializer',
	'DirConstantsInitializer',
	'ContextInitializer',
//	'AppInitializerDelegate',
	'ErrorHandlerInitializer',
	'DispatcherInitializer',
];
echo __PERMINATOR__;
foreach ($initializerList as $name) {
	/** @var Initializer $initializer */
	require_once $name.'.php';

	$class = 'classes\initializer\\'.$name;
	$initializer = new $class();
	$initializer->init();
}

