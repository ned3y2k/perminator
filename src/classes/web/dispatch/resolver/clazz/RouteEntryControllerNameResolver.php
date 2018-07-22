<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-03
 * Time: 오후 3:02
 */

namespace classes\web\dispatch\resolver\clazz;


class RouteEntryControllerNameResolver implements IControllerClassNameResolver {
	function resolve(string $providedClassName = null): string {
		$entryScript = '/index.php';
		$entryScriptLen = strlen($entryScript);

		if (_SELF_ != $entryScript && file_exists(_APP_ROOT_ . _SELF_)) {
			$resolver = new ScriptFileControllerNameResolver();
			return $resolver->resolve($providedClassName);
		}

		if (!$providedClassName) {
			$invoked = substr(trim(_SELF_, " \t\n\r\x0B/"), 0);
			if ($invoked == substr($entryScript, 1)) {
				$invoked = '';
			}
			$className = basename($invoked);
			$namespace = "app\\classes\\controller\\" . str_replace('/', "\\", substr($invoked, $entryScriptLen, -strlen($className)));

			$dir = _APP_ROOT_ . str_replace("\\", DIRECTORY_SEPARATOR, $namespace . $className);

			if (is_dir($dir)) {
				$fullName = $namespace . $className . "\\IndexController";
			} else {
				$fullName = $namespace . ucwords($className) . "Controller";
			}

			$fullName = trim(str_replace('\\\\', '\\', $fullName));
		} else {
			$fullName = $providedClassName;
		}

		return $fullName;
	}
}