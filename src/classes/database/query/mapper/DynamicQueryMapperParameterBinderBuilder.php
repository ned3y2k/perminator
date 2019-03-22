<?php
/**
 * BITMOBILE.
 * 작성자: Kyeongdae
 * 일자: 14. 9. 15
 * 시간: 오전 10:07
 */

namespace classes\database\query\mapper;

use classes\database\query\mapper\exception\DynamicQueryMapperParameterBinderBuilderException;


/**
 * Class DynamicQueryMapperParameterBinderBuilder
 *
 * @package classes\database\query\mapper
 */
class DynamicQueryMapperParameterBinderBuilder {
	/** 파일로 부터 */
	const TYPE_FILE = 0;
	/** 문자열로 부터 */
	const TYPE_STRING = 1;

	/**
	 * @param       $queryId
	 * @param array $variables
	 * @param       $mapperContent
	 * @param int   $type
	 * @throw DynamicQueryMapperParameterBinderBuilderException
	 * @return DynamicQueryMapperParameterBinder
	 */
	public static function buildFunction($queryId, array $variables = null, $mapperContent, $type = self::TYPE_FILE) {
		try {
			$mapper = DynamicQueryMapperBuilder::build($mapperContent, $type);
		} catch (\Exception $ex) {
			throw new DynamicQueryMapperParameterBinderBuilderException($ex->getFile() . ':' . $ex->getLine() . ' ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		return new DynamicQueryMapperParameterBinder($mapper, $queryId, $variables);
	}

	/**
	 * @param       $queryId
	 * @param array $variables
	 * @param       $mapperContent
	 * @param int   $type
	 * @throw DynamicQueryMapperParameterBinderBuilderException
	 * @return DynamicQueryMapperParameterBinder
	 */
	public static function buildProcedure($queryId, array $variables = null, $mapperContent, $type = self::TYPE_FILE) {
		try {
			$mapper = DynamicQueryMapperBuilder::build($mapperContent, $type);
		} catch (\Exception $ex) {
			throw new DynamicQueryMapperParameterBinderBuilderException($ex->getMessage(), $ex->getCode(), $ex);
		}

		return new DynamicQueryMapperParameterBinder($mapper, $queryId, $variables, DynamicQueryMapperParameterBinder::TYPE_PROCEDURE);
	}
} 