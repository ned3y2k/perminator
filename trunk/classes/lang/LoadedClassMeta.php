<?php

namespace classes\lang;

/**
 * TODO 아직 사용 안됨
 * @author User
 *
 */
class LoadedClassMeta {
	private $namespace;
	private $className;

	/**
	 *
	 * @return the $namespace
	 */
	public function getNamespace() {
		return $this->namespace;
	}

	/**
	 *
	 * @return the $className
	 */
	public function getClassName() {
		return $this->className;
	}
	public function getFullName() {
		return $this->namespace + "\\" + $this->className;
	}
	public function __construct() {
		if (func_num_args () == 1) {
			$this->initByFullName ( func_get_arg ( 0 ) );
		} elseif (func_num_args () == 2) {
			$this->initByNamespaceAndClassName ( func_get_arg ( 0 ), func_get_arg ( 1 ) );
		} else {
			throw new \InvalidArgumentException ( "Only one or two message(s) can be passed." );
		}
	}
	private function initByFullName($fullName) {
		$this->namespace = substr ( $fullName, 0, strrpos ( $fullName, "\\" ) );
		$this->className = substr ( $fullName, strrpos ( $fullName, "\\" ) + 1 );
	}
	private function initByNamespaceAndClassName($namespace, $className) {
		$this->namespace = $namespace;
		$this->className = $className;
	}
}
?>