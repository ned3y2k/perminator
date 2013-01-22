<?php
namespace classes\util;
use conf\Core;

class ViewUtil {
	public static function getViewHtml($relativePath) {
		$path = Core::DEFAULT_VIEW_PATH.'/'.$relativePath;
		if(!file_exists($path))
			throw new HtmlFileNotFoundException();
		return file_get_contents($path);
	}
}

class HtmlFileNotFoundException extends \RuntimeException {
}