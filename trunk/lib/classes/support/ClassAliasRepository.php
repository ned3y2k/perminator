<?php
namespace classes\support;
// FIXME ClassAliasRepository 동작 재설계 필요
class ClassAliasRepository {
	private $metaData;
	private $ref;
	public function __construct() {
		$this->metaData = new ClassAliasMeta();
		$this->ref = new \ReflectionObject ( $this->metaData );
	}

	/**
	 *
	 * @param mixed $value
	 * @return string Namespace + ClassName
	 */
	public function findFullClassName($value) {
		if (is_string ( $value )) {
			if ($this->isAlias ( $value )) {
				return $this->getFullNameByAlias ( substr ( $value, 1 ) );
			} else {
				return $value;
			}
		} elseif ($value instanceof ClassAliasMeta) {
			return $value->getFullName ();
		} else {
			throw new \InvalidArgumentException ( gettype ( $value ) . " is " . "not Allowed" );
		}
	}
	private function getFullNameByAlias($value) {
		try {
			return str_replace(".", "\\", $this->ref->getProperty ( $value )->getValue($this->metaData));
		} catch ( \ReflectionException $ex ) {
			throw new AliasNotExist ( $value );
		}
	}
	private function isAlias($value) {
		return substr ( $value, 0, 1 ) == '@';
	}
}
class AliasNotExist extends \RuntimeException {
	public function __construct($message, $code = null, $previous = null) {
		parent::__construct ( $message, $code, $previous );
		$this->message + "is not exist";
	}
}
?>