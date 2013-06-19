<?php
define('DEBUG', false);
require_once 'perminator.php';

use classes\web\script\DispatcherScript;
$Dispatcher = new DispatcherScript();

$Dispatcher->doDispatch();