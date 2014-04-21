<?php
namespace classes\util;

class TypeMeta {
	public $fileName;
	public $typeName;
}

class TypeScanner {
	public static function scan(array $phpScriptList, array $typeList, array $excludeFiles = null, array $excludeTypeList = null) {
		if(is_null($excludeFiles)) $excludeFiles = get_included_files();

		if(is_null($excludeTypeList)) $excludeTypeList = array_merge(get_declared_classes(), get_declared_interfaces());
		else {
			self::realizeTypeNameList($excludeTypeList);
		}
		self::realizeTypeNameList($typeList);
		$excludeFiles = array_merge($typeList, $excludeFiles);

		self::loadScripts($phpScriptList, $excludeFiles);
		return self::findTypes($typeList, $excludeTypeList);
	}

	private static function realizeTypeNameList(&$typeList) {
		foreach ($typeList as &$type) {
			$type = ltrim($type, '\\');
		}
	}

	private static function loadScripts($phpScriptList, $excludeFiles) {
		foreach ($phpScriptList as $phpScript) {
			if(!in_array($phpScript, $excludeFiles)) require_once $phpScript;
		}
	}

	private static function findTypes(array $searchTypeList, array $excludeTypeList) {
		$correctTypes = array();

		$foundTypeList = array_merge(get_declared_classes(), get_declared_interfaces());

		foreach ($foundTypeList as $type) {
			if(in_array($type, $excludeTypeList)) continue;

			if(self::hasTypeList($type, $searchTypeList)) {
				$correctTypes[] = $type;
			}
		}

		return $correctTypes;
	}

	private static function hasTypeList($type, $compareTypeList) {
		$result = false;
		$refType = new \ReflectionClass($type);

		if($refType->isInterface()) {
			foreach ($compareTypeList as $compareType) {
				$result = self::isSubInterface($refType, $compareType);
				if($result) break;
			}
		} else {
			foreach ($compareTypeList as $compareType) {
				$result = self::isSubClass($refType, $compareType);
				if($result) break;
			}
		}

		return $result;
	}

	private static function isSubInterface(\ReflectionClass $refSubInterface, $interfaceName) {
		$result = false;

		$refInterface = new \ReflectionClass($interfaceName);
		if($refInterface->isInterface() && $refInterface->isSubclassOf($refSubInterface->getName())) $result = true;
		unset($refInterface);

		return $result;
	}

	private static function isSubClass(\ReflectionClass $refSubClass, $className) {
		$result = false;

		$refClass = new \ReflectionClass($className);

		if(($refClass->isInterface() && $refSubClass->implementsInterface($className)) || $refSubClass->isSubclassOf($className)) $result = true;
		unset($refClass);

		return $result;
	}
}
