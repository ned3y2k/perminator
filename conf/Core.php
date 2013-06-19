<?php
namespace conf;

class Core {
	const REQUEST_MAP_INFLATOR = '\inflator\config\json\RequestMapInflator';
	const ENTRY_SCRIPT = "index.php";
	const DEFAULT_VIEW_PATH = "resources/views/";
	const DEFAULT_CONTROLLER = "IndexController";
	const CONTROLLER_DEFAULT_METHOD = "index";
	const CONTROLLER_NAMESPACE_PREFIX = '\app\controller\\';
	const DEFAULT_CHARSET = "UTF-8";
	const CACHE_MANAGER_CLASS = '\classes\cache\APCCacheManager';
}