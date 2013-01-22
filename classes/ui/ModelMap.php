<?php

namespace classes\ui;

use classes\util\Assert;

class ModelMap {
	private $map;

	public function __construct() {
		if(is_array(func_get_arg(0)))
			$this->map = func_get_arg(0);
		else 
			$this->map = array();
	}
		
	/**
	 * Add the supplied attribute under the supplied name.
	 * @param attributeName the name of the model attribute (never <code>null</code>)
	 * @param attributeValue the model attribute value (can be <code>null</code>)
	 */
	public function addAttribute($attributeName, $attributeValue) {		
		Assert::notNull($attributeName, "Model attribute name must not be null");
		
		$this->map[$attributeName] = $attributeValue;
		return $this;
	}
	
	/**
	 * Copy all attributes in the supplied <code>Collection</code> into this
	 * <code>Map</code>, using attribute name generation for each element.
	 * @see #addAttribute(Object)
	 */
	public function addAllAttributes(array $attributeValues) {
		if (attributeValues != null) {
			foreach ($attributeValues as $key => $value) {
				$this->map[$key] = $value;;
			}
		}
		return this;
	}
	
	
	/**
	 * Does this model contain an attribute of the given name?
	 * @param attributeName the name of the model attribute (never <code>null</code>)
	 * @return whether this model contains a corresponding attribute
	 */
	public function containsAttribute($attributeName) {
		return array_key_exists ( $attributeName, $this->map );
	}

	public function get($attributeName) {
		Assert::notNull($attributeName, "Model attribute name must not be null");
		Assert::arrayNotHasKey($attributeName, $this->map, "this Model the not has Key.");
		
		return $this->map[$$attributeName];
	}
}