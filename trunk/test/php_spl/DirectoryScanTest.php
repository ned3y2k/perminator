<?php
use classes\io\scanner\RecursiveDirectoryScanner;
use classes\io\scanner\RecursiveFileFilter;
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'index.php';

/**
 * DirectoryScan test case.
 */
class DirectoryScanTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 * @var DirectoryScan
	 */
	private $DirectoryScan;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		$this->DirectoryScan = null;

		parent::tearDown ();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct() {
	}

	public function testMe() {
		$scanner = new RecursiveDirectoryScanner();
		$scanner->prepare(RecursiveFileFilter::createRegExFilter("/(?:phpunit)?(?:Extensions)|(?:Framework)/i"));
		$results = $scanner->scan(APP_ROOT.'phpunit');

		var_dump($results);
	}
}

