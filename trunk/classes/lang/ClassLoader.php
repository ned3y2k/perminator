<?php
namespace classes\lang;

use classes\component\factory\AbstractFactory;
use classes\support\ClassAliasRepository;

if (! defined ( 'NIL' ))
	define ( 'NIL', null );

	// require_once $_SERVER ['DOCUMENT_ROOT'] . '/perminator.inc.php';
require_once 'perminator.inc.php';
// require_once 'LoadedClassMeta.php';

/**
 * FIXME ClassAlias 관련 전부 AbstactFactory로 이동 하여야 할것
 *
 * @author User
 *         Perminator Class Loader
 */
class ClassLoader {
	private static $instance = NIL;
	private $includePaths;
	private $includedClasses;
	private $classAliasRepository;
	private $objectFactory;
	public function __construct() {
		global $incPath;

		spl_autoload_register ( array (
				$this,
				"includeByClassName"
		) );
		$this->includePaths = explode ( PATH_SEPARATOR, get_include_path () );
		$this->includePaths = array_merge ( $incPath, $this->includePaths );

		$this->includedClasses = array ();

		$this->classAliasRepository = new ClassAliasRepository ();
		$this->objectFactory = new AbstractFactory ( $this );
	}

	/**
	 *
	 * @param string $fullName
	 *        	Namespace And ClassName
	 * @throws ClassNotFoundException
	 */
	public function includeByClassName($fullName) {
		$foundCheck = false;
		$classPath = str_replace ( "\\", DIRECTORY_SEPARATOR, $fullName ) . ".php";
		foreach ( $this->includePaths as &$includePath ) {
			if (file_exists ( $includePath . '/' . $classPath )) {
				$foundCheck = true;
				include_once $includePath . '/' . $classPath;
				break;
			}
		}

		if (! $foundCheck) {
			throw new ClassNotFoundException ( $fullName . " Not Found" );
		}

		array_push ( $this->includedClasses, $fullName );
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
	 * @return \classes\lang\ClassLoader
	 */
	public static function getClassLoader() {
		if (!is_null(self::$instance) && is_object(self::$instance))
			self::$instance = new ClassLoader ();
		return self::$instance = new ClassLoader ();
	}
}