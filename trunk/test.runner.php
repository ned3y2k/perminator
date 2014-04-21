<?php
use classes\util\TypeScanner;
use classes\io\scanner\RecursiveFileScanner;
use classes\io\scanner\RecursiveFileFilter;
use classes\lang\PerminatorClassLoader;
use classes\context\Context;

if(php_sapi_name() == 'cli') {
	echo "not yet supprt cli";
	exit;
} elseif(!array_key_exists('filePath', $_GET) || !file_exists('test/'.$_GET['filePath'])) {
	echo "test file not found";
	exit;
}

$filePath = $_GET['filePath'];
define("PERMINATOR_TEST", true);

require_once 'index.php';
$php_unit_dir = APP_ROOT.'phpunit'.DIRECTORY_SEPARATOR.'3.7';

set_include_path(get_include_path().PATH_SEPARATOR.$php_unit_dir);
$php_unit_autoload = $php_unit_dir.DIRECTORY_SEPARATOR.'PHPUnit'.DIRECTORY_SEPARATOR."Autoload.php";
require_once $php_unit_autoload;
PerminatorClassLoader::getClassLoader(Context::getSharedContext());



class PerminatorPHPUnitSuite extends PHPUnit_Framework_TestSuite {
	/**
	 *
	 * @return PerminatorPHPUnitSuite
	 */
	public static function suite() {
		$suite = new self();
		$suite->setName(__CLASS__);

		$rootPath = realpath(APP_ROOT.'test');

		$scanner = new RecursiveFileScanner();
		$scanner->prepare(RecursiveFileFilter::createExtNameFilter("php"));

		$types = TypeScanner::scan($scanner->scan($rootPath), array('\PHPUnit_Framework_TestCase'), array(), array());
		foreach ($types as $type) {
			$suite->addTestSuite($type);
		}

		return $suite;
	}
}
PHPUnit_TextUI_TestRunner::run(PerminatorPHPUnitSuite::suite());