<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 6. 30
 * 시간: 오후 11:45
 */
namespace classes\util\object;

class InvisibleFiledHasObjectBuilderCache extends AbstractObjectBuilderCache {
	public function setInVisibleFiled($name, $value) {
		/* @var $refProperty \ReflectionProperty */
		$refProperty = $this->refClass->getProperty($name);
		$refProperty->setAccessible(true);
		$refProperty->setValue($this->object, $value);
	}
}