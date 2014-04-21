<?php
use classes\trouble\exception\core\PHPScriptNotFoundException;

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

function rectify_file_path($path) {
	if(PHP_OS == "WINNT") {
		return str_replace ( "/", DIRECTORY_SEPARATOR, $path );
	} else {
		return str_replace ( "\\", DIRECTORY_SEPARATOR, $path );
	}
}

$scriptRootPathList = array(
		APP_ROOT . 'lib' . DIRECTORY_SEPARATOR,
		APP_ROOT . 'app' . DIRECTORY_SEPARATOR
);

function find_script($relativePath) {
	global $scriptRootPathList;

	foreach ($scriptRootPathList as $scriptRootPath) {
		if (file_exists ( $path = $scriptRootPath . $relativePath ))
			return $path;
	}

	return null;
}