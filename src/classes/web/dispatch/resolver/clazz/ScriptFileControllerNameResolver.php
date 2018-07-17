<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-03
 * Time: 오후 2:53
 */

namespace classes\web\dispatch\resolver\clazz;


class ScriptFileControllerNameResolver implements IControllerClassNameResolver {

	function resolve(string $providedClassName = null): string {
		$namespace = str_replace('/', '\\', dirname(_SELF_)) . '\\';
		if (TEST) {
			$namespace = substr($namespace, strlen(_APP_ROOT_));
		}

		if ($providedClassName === null) {
			$className = basename(_SELF_, '.php');
			$fullName = $namespace . ucwords($className) . "Controller";
			$fullName = trim(str_replace('\\\\', '\\', $fullName));

			$fullName = str_replace('.', '_', $fullName, $count);

			return $fullName;
		} else {
			$fullName = $providedClassName;

			return $fullName;
		}
	}
}