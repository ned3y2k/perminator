<?php
/**
 * 새로 생성된 인스턴스의 필드의 자신에게 할당
 * @param stdClass $instance
 * @param stdClass $newInstance
 * @throws InvalidArgumentException
 */
function object_resign(stdClass &$instance, stdClass $newInstance) {
	if (get_class ( $instance ) && get_class ( $newInstance )) {
		$ref = new ReflectionObject ( $instance );
		$props = $ref->getProperties ();

		foreach ( $props as $prop ) {
			/* @var $prop \ReflectionProperty */
			$isPublic = $prop->isPublic ();
			if (! $isPublic)
				$prop->setAccessible ( true );
			$name = $prop->getName ();
			$instance->$name = $newInstance->$name;
			if (! $isPublic) $prop->setAccessible ( false );
		}
	} else {
		throw new InvalidArgumentException ( 'instances not matched' );
	}
}

/**
 * 객체 안에 있는 내용을 선택한다.
 *
 * <code>
 * 		$a = new MockObject();
 *		echo object_element_select($a, array('getMember','getPartner','name'));
 * </code>
 *
 * @param  array   $array
 * @param  array   $selectors
 * @param  mixed   $default
 * @return mixed
 */
function object_element_select($object, array $selectors, $default = null) {
	if(!is_object($object)) throw new InvalidArgumentException(gettype($object));

	$temp = $object;

	foreach ($selectors as $selector) {
		if(method_exists($object, $selector)) {
			$temp = $temp->$selector();
		} elseif(property_exists(get_class($object), $selector)) {
			$temp = $object->$selector;
		} else {
			return $default;
		}
	}

	return $temp;
}