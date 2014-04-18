<?php
namespace classes\util;
use conf\Core;

class ViewUtil {
	public static function getViewHtml($relativePath) {
		return file_get_contents(self::findViewFilePath($relativePath));
	}

	public static function findViewFilePath($relativePath) {
		$path = Core::DEFAULT_VIEW_PATH.'/'.$relativePath;
		if(!file_exists($path))
			throw new HtmlFileNotFoundException();
		return $path;
	}
}

class HtmlFileNotFoundException extends \RuntimeException {
}