<?php
/**
 * User: Kyeongdae
 * Date: 2018-07-12
 * Time: 오후 5:56
 */

namespace classes\web\dispatch\resolver\clazz;


use PHPUnit\Framework\TestCase;

class RouteEntryControllerNameResolverTest extends TestCase {
	/** @var IControllerClassNameResolver */
	private $resolver;

	protected function setUp() {
		$this->resolver = new RouteEntryControllerNameResolver();
	}

	public function testResolve() {
		var_dump($this->resolver->resolve());
		$this->assertEquals("app\classes\controller\Controller", $this->resolver->resolve());
	}
}
