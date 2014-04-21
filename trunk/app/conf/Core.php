<?php
namespace conf;

class Core {
	const REQUEST_MAP_INFLATOR = '\inflator\config\json\RequestMapInflator';
	const ENTRY_SCRIPT = "index.php";
	const DEFAULT_VIEW_PATH = "resources/views/";
	const DEFAULT_CONTROLLER = "IndexController";
	const CONTROLLER_DEFAULT_METHOD = "index";
	const CONTROLLER_NAMESPACE_PREFIX = '\classes\\controller\\';
	const DEFAULT_CHARSET = "UTF-8";
	const CACHE_MANAGER_CLASS = '\classes\cache\APCCacheManager';
	const EXCEPTION_HANDLER = '\classes\trouble\ExceptionHandler';
	const EXCEPTION_PRINTER = '\classes\trouble\printer\XMLExceptionPrinter';
	const REQUEST_MAP_GENERATE = true;
}