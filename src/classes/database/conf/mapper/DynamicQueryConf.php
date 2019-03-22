<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 2014-11-26
 * 시간: 오후 2:04
 */
namespace classes\database\conf\mapper;


class DynamicQueryConf {
	const CACHE_MANAGER = '\classes\database\query\mapper\cache\BuiltinPdoMysqlCacheManager';
//	const CACHE_MANAGER = '\classes\database\query\mapper\cache\BuiltinMysqlCacheManager';
//	const CACHE_MANAGER = '\classes\cache\ApcCacheManager';
}