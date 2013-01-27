<?php
namespace lib\mysqli;

use classes\binder\DataBinder;

class MySQLIStatement extends \mysqli_stmt {
	private $fieldMap = array();

	function __construct($link,$query) {
		parent::__construct($link,$query);
	}

	public function addFieldMaps($dbFieldName, $properyName) {
		$this->fieldMap[$dbFieldName] = $properyName;
	}

	public function fetchObjects($class) {
		$objects = array();

		while(($row = $this->fetchObejct($class)) != null) {
			$objects[] = $row;
		}

		return $objects;
	}

	public function fetchObejct($class) {
		$fieldMapCount = count($this->fieldMap);
		$this->isMissingFieldMap ( $fieldMapCount );

		$tempRowArray = $this->fetchArray ( $fieldMapCount );
		$fieldMetas = $this->getFields();

		if(is_null($tempRowArray))
			return null;

		$data = array();
		for($i = 0; $i < $fieldMapCount; $i ++) {
			$data[$this->findObjectPropertyNameByDbFieldName($fieldMetas[$i]->name)] = $tempRowArray[$i];
		}

		$dataBinder = new DataBinder();
		$instance = new $class();
		$dataBinder->binding($instance, $data);

		return $instance;
	}

	private function findObjectPropertyNameByDbFieldName($fieldName) {
		return $this->fieldMap[$fieldName];
	}

	/**
	 * @param fieldMapCount
	 */
	 private function isMissingFieldMap($fieldMapCount) {
		if ($fieldMapCount != $this->field_count)
			throw new \RuntimeException ( "Missing field map. you must addFieldMap." );
	}

	/**
	 * @param fieldMapCount
	 */
	 private function fetchArray($fieldMapCount) {
		$ref = new \ReflectionObject ( $this );
		$tempRowArray = range ( 0, $fieldMapCount - 1 );
		$ref->getMethod ( "bind_result" )->invokeArgs($this,  $tempRowArray);
		$this->fetch();

		for($i = 0; $i < $fieldMapCount; $i ++) {
			if(!is_null($tempRowArray[$i]))
				return $tempRowArray;
		}

		return null;
	}

	public function getFields() {
		$fieldMetas = array();
		$result_meta = $this->result_metadata ();
		while ( ($field = $result_meta->fetch_field ()) != false ) {
			$fieldMeta = new FieldMeta();
			$fieldMeta->name = $field->name;
			$fieldMeta->orgName = $field->orgname;
			$fieldMeta->type = $field->type;
			$fieldMeta->orgTable = $field->orgtable;
			$fieldMeta->table = $field->table;

			$fieldMetas[] = $fieldMeta;
		}
		$result_meta->free ();

		return $fieldMetas;
	}
}