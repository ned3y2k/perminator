<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 14. 9. 15
 * 시간: 오전 10:24
 */
namespace lib\db\query\mapper;

use classes\database\query\mapper\DynamicQueryMapperParameterBinderBuilder;
use classes\test\BitTestCase;

class TestDynamicQueryMapperParameterBinderBuilder extends BitTestCase {
	const XML_FILE = 'test/lib/db/query/mapper/TestDynamicQueryMapperParameterBinderBuilder.xml';

	public function test_buildFunction()
    {
        $vars = array('title'=>'Nara', 'offset'=>0, 'rowCount'=>15);
        $paramBinder = DynamicQueryMapperParameterBinderBuilder::buildFunction('selectRows', $vars, self::XML_FILE);
        var_dump($paramBinder);
    }
}