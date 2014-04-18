<?php
namespace classes\lang;

use classes\component\factory\AbstractFactory;
use classes\support\ClassAliasRepository;
use classes\trouble\exception\core\ClassNotFoundException;
use classes\trouble\exception\core\PHPScriptNotFoundException;

// require_once $_SERVER ['DOCUMENT_ROOT'] . '/perminator.inc.php';
require_once 'perminator.conf.php';
// require_once 'LoadedClassMeta.php';

/**
 * FIXME ClassAlias 관련 전부 AbstactFactory로 이동 하여야 할것
 *
 * @author User
 *         Perminator Class Loader
 */
class PerminatorClassLoader {
	private $includePaths;
	private $includedClasses = array();
	private $classAliasRepository;
	private $objectFactory;

	public function __construct(\Context $context) {
		spl_autoload_register ( array ($this, "includeByClassName") );
		$this->includePaths = array_merge ($context->getIncludePaths(), explode ( PATH_SEPARATOR, get_include_path () ) );

		$this->classAliasRepository = new ClassAliasRepository ();
		$this->objectFactory = new AbstractFactory ( $this );
	}

	/**
	 *
	 * @param string $fullName
	 *        	Namespace And ClassName
	 * @throws \classes\trouble\exception\core\ClassNotFoundException
	 */
	public function includeByClassName($fullName) {
		try {
			load_lib ( $fullName );
			if(!class_exists($fullName) && !interface_exists($fullName)) {
				header('Content-type: text/plain; charset=utf-8');
				debug_print_backtrace();
				echo "not found script file name or conflict: {$fullName}";
			}
		} catch ( PHPScriptNotFoundException $ex ) {
			throw new ClassNotFoundException($fullName);
		}
	}

	/**
	 *
	 * @param mixed $fullName
	 * @return object
	 */
	public function newInstance(&$fullName) {
		$fullName = $this->findFullClassName ( $fullName );
		return $this->objectFactory->newInstance ( $fullName );
	}
	public function findFullClassName(&$name) {
		return $this->classAliasRepository->findFullClassName ( $name );
	}

	/**
	 * Return a ClassLoader.(Singlton)
	 *
	 * @return \classes\lang\PerminatorClassLoader
	 */
	public static function getClassLoader(\Context $context) {
		static $instance = null;

		return is_null($instance) ? $instance = new self($context) : $instance;
	}
}