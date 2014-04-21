<?php
namespace classes\context;

use classes\io\scanner\RecursiveFileScanner;
use classes\io\scanner\RecursiveFileFilter;
use classes\util\TypeScanner;

class ComponentScanner {
	private  $excludeFiles;

	public function scan($rootPath) {
		$rootPath = realpath(APP_ROOT.DIRECTORY_SEPARATOR.$rootPath);

		$scanner = new RecursiveFileScanner();
		$scanner->prepare(RecursiveFileFilter::createExtNameFilter("php"));

		$types = TypeScanner::scan($scanner->scan($rootPath), array('\classes\stereotype\Component'));

		return $types;
	}
}