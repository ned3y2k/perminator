<?php
namespace component\database;
use classes\util\ObjectBuilderUtil;

interface IDatabaseStatement {
	const RESULT_OBJECT = 0;
	const RESULT_ARRAY = 1;
	const DEFAULT_CLASS_NAME = '\stdClass';

	function getAffectedRows();
	function getInsertId();
	function getNumRows();
	function getErrno();
	function getError();
	function getSqlstate();
	static function prepare($connection, $query, $reusable = false);
	function close();
	function dataSeek($offset);
	function freeResult();
	function reset();
	function storeResult();
	function bindParam();
	function execute();
	function fetchArray(array $iteratorMap = null);
	function fetchObject($className = self::DEFAULT_CLASS_NAME, ObjectBuilderUtil $objectBuilder = null, array $iteratorMap = null);
	function executeAndFetch($className = self::DEFAULT_CLASS_NAME, $type = self::RESULT_OBJECT, array $iteratorMap = null);
}

class ValueExtractor {
	public $fieldName;
	public $closure;

	public function __construct($fieldName, \Closure $resolverFunc) {
		$this->fieldName = $fieldName;
		$this->closure = $resolverFunc;
	}
}