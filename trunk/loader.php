<?php
use classes\trouble\exception\core\PHPScriptNotFoundException;
function rectify_file_path($path) {
	if(PHP_OS == "WINNT") {
		return str_replace ( "/", DIRECTORY_SEPARATOR, $path );
	} else {
		return str_replace ( "\\", DIRECTORY_SEPARATOR, $path );
	}
}
function load_lib($relativePath) {

	$fileName = rectify_file_path ( $relativePath ) . ".php";

	if (!is_null($script = find_script($fileName))) {
		if(!in_array($script, get_included_files())) {
			require_once $script;
		}
		return;
	} elseif (!is_null($combined_script = find_script (dirname ( $fileName ) . ".php" )) && !in_array($combined_script, get_included_files())) {
		if(!in_array($combined_script, get_included_files())) {
			require_once $combined_script;
		}
		return;
	}

	throw new PHPScriptNotFoundException($fileName);
}

$appLibRootPath = APP_ROOT . 'app' . DIRECTORY_SEPARATOR;
$systemLibRootPath = APP_ROOT . 'lib' . DIRECTORY_SEPARATOR;
function find_script($relativePath) {
	global $appLibRootPath, $systemLibRootPath;

	if (file_exists ( $path = $appLibRootPath . $relativePath )) {
		return $path;
	} elseif (file_exists ( $path = $systemLibRootPath . $relativePath )) {
		return $path;
	}

	return null;
}