<?php
define('DEBUG', false);

use classes\web\script\DispatcherScript;
use classes\lang\ClassLoader;

include_once 'classes/lang/ClassLoader.php';
include_once 'classes/web/script/DispatcherScript.php';

ClassLoader::getClassLoader();
$Dispatcher = new DispatcherScript();

$Dispatcher->doDispatch();