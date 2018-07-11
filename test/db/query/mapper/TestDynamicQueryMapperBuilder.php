<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 14
 * 시간: 오후 10:42
 */
namespace lib\db\query\mapper;

use classes\database\query\mapper\DynamicQueryMapperBuilder;
use classes\test\BitTestCase;

if(!defined('BITMOBILE')) define('BITMOBILE', null);
require_once getenv('PROJECT_LOCATION') . '/lib/init.php';

class TestDynamicQueryMapperBuilder extends BitTestCase {
	const XML_FILE = 'test/lib/db/query/mapper/TestDynamicQueryMapperBuilder.xml';

	function test_buildString() {
	    $xmlPath = __DIR__.DIRECTORY_SEPARATOR.basename(__FILE__, '.php'.'.xml');
        var_dump(DynamicQueryMapperBuilder::build(file_get_contents($xmlPath), DynamicQueryMapperBuilder::TYPE_STRING));
    }

    function test_buildFile() {
        DynamicQueryMapperBuilder::build(self::XML_FILE, DynamicQueryMapperBuilder::TYPE_FILE);
    }
} 