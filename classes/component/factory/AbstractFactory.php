<?php
namespace classes\component\factory;

use classes\lang\ClassLoader;

class AbstractFactory {
	private $classLoader;
	public function __construct(ClassLoader $classLoader) {
		$this->classLoader = $classLoader;
	}

	/**
	 *
	 * @param mixed $fullName
	 *        	Namespace And ClassName
	 * @throws ClassNotFoundException
	 * @return object
	 */
	public function newInstance(&$fullName) {
		$argsNum = func_num_args ();

		if ($argsNum == 1) {
			return new $fullName ();
		} elseif ($argsNum >= 2) {
			$reflection = new \ReflectionClass ( $fullName );
			array_shift ( $args );
			return $reflection->newInstanceArgs ( $args );
		} else {
			throw new \InvalidArgumentException(); // TODO 이쪽 메시지 확실히 할것!
		}
	}
}

?>