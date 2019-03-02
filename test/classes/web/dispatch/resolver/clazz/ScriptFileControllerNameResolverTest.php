<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-12
 * Time: 오후 5:49
 */

namespace classes\web\dispatch\resolver\clazz;

use PHPUnit\Framework\TestCase;

class ScriptFileControllerNameResolverTest extends TestCase {
	/** @var IControllerClassNameResolver */
	private $resolver;

	protected function setUp() {
		$this->resolver = new ScriptFileControllerNameResolver();
	}

	public function testResolve() {
		var_dump($this->resolver->resolve());
		$this->assertEquals(
			"vendor\phpunit\phpunit\PhpunitController",
			$this->resolver->resolve()
		);
	}
}
