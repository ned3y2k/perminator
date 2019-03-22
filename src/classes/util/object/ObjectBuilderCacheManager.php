<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 7. 1
 * 시간: 오전 12:01
 */
namespace classes\util\object;

use classes\io\exception\DirectoryNotFoundException;
use classes\io\exception\PermissionException;
use classes\io\File;
use classes\lang\ArrayUtil;

define('_DIR_OBJECT_BUILDER_CACHE', _DIR_CACHE_ROOT_ . 'object_builder' . DIRECTORY_SEPARATOR);
if (!file_exists(_DIR_OBJECT_BUILDER_CACHE)) mkdir(_DIR_OBJECT_BUILDER_CACHE, 7666);

class ObjectBuilderCacheManager {
	const SEPARATOR = '_C_';

	/**
	 * @param string $className
	 *
	 * @return AbstractObjectBuilderCache|null
	 */
	public static function get($className) {
		$cacheName = self::createName($className);

		if (!TEST && self::loadCache($cacheName)) { // 테스트 시에는 무작정 Object 캐쉬를 다시 생성한다.  물론 create를 실행하여 다시 생성된다
			/* @var $builderCache AbstractObjectBuilderCache */
			$builderCache = new $cacheName();

			return $builderCache;
		} elseif ($className == '\stdClass') {
			return new stdClassObjectBuilderCache();
		} else {
			return null;
		}
	}

	public static function delete($className) {
		$fileName = self::createCacheFileName(self::createName($className));
		@unlink($fileName);
	}

	/**
	 * @param string $className
	 * @param array $propertyNames
	 * @param array $inVisibleFiledNames
	 * @param array $visibleFieldNames
	 *
	 * @return AbstractObjectBuilderCache|null
	 * @throws DirectoryNotFoundException
	 * @throws PermissionException
	 */
	public static function create($className, array $propertyNames, array $inVisibleFiledNames, array $visibleFieldNames) {
		$cacheName = self::createName($className);

		if (count($inVisibleFiledNames) != 0) {
			$content = self::createHasInVisibleObjectCache($className, $propertyNames, $inVisibleFiledNames, $visibleFieldNames, $cacheName);
		} else {
			$content = self::createObjectCache($className, $propertyNames, $inVisibleFiledNames, $visibleFieldNames, $cacheName);
		}

		File::writeAllText($fileName = self::createCacheFileName($cacheName), $content);
		/** @noinspection PhpIncludeInspection */

		self::loadCache($cacheName);

		return new $cacheName();
	}

	private static function createCacheFileName($cacheName) {
		return _DIR_OBJECT_BUILDER_CACHE . $cacheName . '.php';
	}

	private static function createName($className) {
		$className = str_replace("\\", self::SEPARATOR, $className);

		return 'cache_' . $className;
	}

	/**
	 * 캐쉬는 클래스 로더로 불러올수 없기 떄문에 요것을 사용한다.
	 * @param string $cacheName
	 *
	 * @return bool
	 */
	private static function loadCache($cacheName) {
		$filePath = self::createCacheFileName($cacheName);
		LoadedCacheList::getInstance()->push($filePath);

		if (File::exists($filePath)) {
			/** @noinspection PhpIncludeInspection */
			require_once create_path($filePath);

			return true;
		}

		return false;
	}

	/**
	 * @param       $className
	 * @param array $propertyNames
	 * @param array $inVisibleFiledNames
	 * @param array $visibleFieldNames
	 * @param       $cacheName
	 *
	 * @return string
	 */
	private static function createHasInVisibleObjectCache($className, array $propertyNames, array $inVisibleFiledNames, array $visibleFieldNames, $cacheName) {
		$content = "<?php\nclass {$cacheName} extends " . '\classes\util\object\InvisibleFiledHasObjectBuilderCache {';

		$content .= "\n\tpublic function __construct() {\n";
		$content .= "\t\t\$this->refClass = new \ReflectionClass('{$className}');";

		foreach ($inVisibleFiledNames as $key => $val) {
			$content .= "\n\t\t\$this->inVisibleFieldNames[] = '{$key}';";
		}

		foreach ($visibleFieldNames as $key => $val) {
			$content .= "\n\t\t\$this->fieldNames[] = '{$key}';";
		}

		foreach ($propertyNames as $key => $propertyName) {
			$content .= "\n\t\t\$this->propertyNames['{$key}'] = array('setter'=>'{$propertyName['setter']}', 'getter'=>'{$propertyName['getter']}');";
		}

		$content .= "\n\t}\n}";

		return $content;
	}

	private static function createObjectCache($className, $propertyNames, $inVisibleFiledNames, $visibleFieldNames, $cacheName) {
		$content = "<?php\nclass {$cacheName} extends {$className} {\n\tprivate static \$init = false;\n\n\tprivate static \$propertyNames = array();\n\tprivate \$object = array();\n";

		$content .= "\tpublic function isField(\$name) { return false; }\n";
		$content .= "\tpublic function isInvisibleField(\$name) { return false; }\n";
		$content .= "\tpublic function isProperty(\$name) { return true; }\n";

		$content .= "\tpublic function setObject(\$object) {\n";
		$content .= "\t\tif(!is_object(\$object)) throw new \\InvalidArgumentException('not object');\n";
		$content .= "\t\t\$this->object = \$object;\n";

		foreach ($propertyNames as $propertyName) {
			$setterName = $propertyName[ 'setter' ];
			$getterName = $propertyName[ 'getter' ];
			$fieldName  = ArrayUtil::getValue($propertyName, 'field');
			$content .= "\n\t\ttry {";
			if ($getterName == null) {
				$content .= "\n\t\t\t\$this->{$setterName}(object_get_private_field(\$object, '{$fieldName}'));";

			} elseif ($setterName == null) {
				$content .= "\n\t\t\tobject_set_private_field(\$this, '{$fieldName}', \$object->{$getterName}());";
			} else {
				$content .= "\n\t\t\t\$this->{$setterName}(\$object->{$getterName}());";
			}
			$content .= "\n\t\t} catch (\Exception \$ex) {}\n";
		}
		foreach ($visibleFieldNames as $key => $val) {
			$content .= "\n\t\t\$this->{$key} = \$this->object->{$key};";
		}

		$content .= "\n\t}\n";

		$content .= "\n\tpublic function getObject() { return \$this; }";

		$content .= "\n\tpublic function __construct() {";
		$content .= "\n\t\tif(!self::\$init) {";
		foreach ($propertyNames as $key => $propertyName) {
			$isPrivate = $propertyName[ 'private' ] ? 'true' : 'false';

			if (ArrayUtil::getValue($propertyName, 'setter') == null) {
				$content .= "\n\t\t\tself::\$propertyNames['{$key}'] = array('setter'=>null, 'getter'=>'{$propertyName['getter']}', 'private'=>{$isPrivate});";
			} elseif (ArrayUtil::getValue($propertyName, 'getter') == null) {
				$content .= "\n\t\t\tself::\$propertyNames['{$key}'] = array('setter'=>'{$propertyName['setter']}', 'getter'=>null, 'private'=>{$isPrivate});";
			} else {
				$content .= "\n\t\t\tself::\$propertyNames['{$key}'] = array('setter'=>'{$propertyName['setter']}', 'getter'=>'{$propertyName['getter']}', 'private'=>{$isPrivate});";
			}
		}

		foreach ($visibleFieldNames as $key => $val) {
			$setterName = 'set' . ucfirst($key);
			$content .= "\n\t\t\tself::\$propertyNames['{$key}'] = '{$setterName}';";

			$visibleFieldNames[ $key ] = $setterName;
		}
		$content .= "\n\t\t\tself::\$init = true;\n\t\t}\n\t}";

		foreach ($visibleFieldNames as $key => $val) {
			$content .= "\n\tpublic function {$val}(\$value) { \$this->$key = \$value; }";
		}

		$content .= "\n\tpublic function setProperty(\$name, \$value) {\n";
		$content .= "\t\tif(!array_key_exists(\$name, self::\$propertyNames)) { \$this->\$name = \$value; return;}\n";
		$content .= "\t\t\$setterName = is_array(self::\$propertyNames[\$name]) ? self::\$propertyNames[\$name]['setter'] : self::\$propertyNames[\$name];\n\n";
		$content .= "\t\tif(\$setterName === null && self::\$propertyNames[\$name]['private']) object_set_private_field(\$this, \$name, \$value);\n";
		$content .= "\t\telseif(\$setterName === null && !self::\$propertyNames[\$name]['private']) \$this->\$name = \$value;\n";
		$content .= "\t\telse \$this->\$setterName(\$value);\n";
		$content .= "\t}";

		$content .= "\n}";

		return $content;
	}
}

class stdClassObjectBuilderCache extends AbstractObjectBuilderCache {
	public function isField($name) { return true; }

	public function isInvisibleField($name) { return false; }

	public function isProperty($name) { return false; }

	public function setProperty(/** @noinspection PhpUnusedParameterInspection */
		$name, /** @noinspection PhpUnusedParameterInspection */
		$value) {
		throwNewUnimplementedException();
	}

	public function setField($name, $value) { $this->object->$name = $value; }

	public function setInVisibleFiled($name, $value) { throwNewUnimplementedException(); }
}