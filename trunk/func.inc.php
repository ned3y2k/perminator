<?php
use classes\lang\UnsupportedOperationException;
/**
 * @return string
 */
function initCacheDir() {
	$cacheDir = $_SERVER ["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . "cache";
	if (! file_exists ( $cacheDir ))
		mkdir ( $cacheDir );

	return $cacheDir;
}
function unlink_recursive($dir, $deleteRootToo = true) {
	if (!file_exists($dir) || ! $dh = @opendir ( $dir )) {
		return;
	}

	while ( false !== ($obj = readdir ( $dh )) ) {
		if ($obj == '.' || $obj == '..') {
			continue;
		}

		if (! @unlink ( $dir . DIRECTORY_SEPARATOR . $obj )) {
			unlink_recursive ( $dir . DIRECTORY_SEPARATOR . $obj, true );
		}
	}

	closedir ( $dh );

	if ($deleteRootToo) {
		@rmdir ( $dir );
	}

	return;
}
function unsupported_operation() {
	throw new UnsupportedOperationException();
}