<?php
use classes\context\support\ComponentScanner;
use classes\io\scanner\RecursiveFileScanner;
use classes\io\scanner\RecursiveFileFilter;
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'index.php';

/**
 * ComponentFinder test case.
 */
class ComponentScannerTest extends PHPUnit_Framework_TestCase {

	/**
	 *
	 * @var componentScanner
	 */
	private $componentScanner;

	/**
	 * Prepares the environment before running a test.
	 */
	protected function setUp() {
		parent::setUp ();

		// TODO Auto-generated ComponentScanTest::setUp()

		$this->componentScanner = new ComponentScanner();
	}

	/**
	 * Cleans up the environment after running a test.
	 */
	protected function tearDown() {
		// TODO Auto-generated ComponentScanTest::tearDown()
		$this->componentScanner = null;

		parent::tearDown ();
	}

	/**
	 * Constructs the test case.
	 */
	public function __construct() {
		// TODO Auto-generated constructor
	}

	public function testFileScanner() {
		$scanner = new RecursiveFileScanner();
		$scanner->prepare(RecursiveFileFilter::createExtNameFilter("php"));
		$results = $scanner->scan(APP_ROOT."/app/classes");

		$this->assertTrue(is_array($results), "not valid");
		$this->assertNotEquals(0, count($results), "no results");
		//var_dump("filescanner",$results);
	}

	public function testScan() {
		print_r($this->componentScanner->scan('app/classes'));
	}
}