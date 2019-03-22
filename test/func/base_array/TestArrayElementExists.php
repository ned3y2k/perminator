<?php
/**
 * User: Kyeongdae
 * Date: 2016-12-15
 * Time: 오후 9:16
 */
namespace lib\func\base_array;

use classes\lang\ArrayUtil;
use classes\test\BitTestCase;

class TestArrayElementExists extends BitTestCase {
	private $arr = [];


	/**
	 * TestArrayElementExists constructor.
	 */
	public function setUp() {
		$this->arr = [
			'item5852875a641e5' => [
				'var' => 1,
				'list'=>[
					'var1'=>1
				]
			],
			'item5852875a64235' => ['var' => 2,],
			'item5852875a6424c' => ['var' => 3],
		];

		$this->arr['list'] = [
			0 => ['var' => 1],
			1 => ['var' => 2],
			2 => ['var' => 3]
		];
	}

	public function testTrue() {
		$selector = ['item5852875a641e5', 'list', 'var1'];
		$this->assertTrue(ArrayUtil::existsMultiDimensionalKey($selector, $this->arr));
	}

	public function testFalse() {
		$selector = ['item5852875a6424c', 'var', 'a1'];
		$this->assertFalse(ArrayUtil::existsMultiDimensionalKey($selector, $this->arr));
	}
}