<?php
namespace lib\mysqli;

use classes\binder\DataBinder;


class MySQLIStatement extends \mysqli_stmt {
	private $fieldMaps = array();
	private $query = null;
	private $link;

	function __construct($link,$query) {
		parent::__construct($link,$query);
		$this->link = $link;
		$this->query = $query;
	}

	public function addFieldMaps($fieldMaps) {
		$this->fieldMaps = array_merge($this->fieldMaps, $fieldMaps);
	}

	public function addFieldMap($dbFieldName, $properyName) {
		$this->fieldMaps[$dbFieldName] = $properyName;
	}

	public function fetchObjects($class) {
		$objects = array();

		while(($row = $this->fetchObject($class)) != null) {
			$objects[] = $row;
		}

		$this->free_result();
		$this->close();
		return $objects;
	}

	public function fetchObject($class) {
		$fieldMapCount = count($this->fieldMaps);
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
		return $this->fieldMaps[$fieldName];
	}

	/**
	 * @param fieldMapCount
	 */
	 private function isMissingFieldMap($fieldMapCount) {
		if ($fieldMapCount != $this->field_count)
			throw new \RuntimeException ( "Missing field map. you must addFieldMap.\n ".(int)$this->field_count." has a fetch result field\nMap count: {$fieldMapCount}" );
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

	public function execute() {
		parent::execute();

		if($this->errno) {
			$msg = DEBUG == true ? $this->error."\nquery: ".$this->query : $this->error;
			throw new \mysqli_sql_exception($msg, $this->errno);
		}
	}

	public function executeNonQuery() {
		$this->execute();
		$resut = $this->affected_rows;
		$this->free_result();
		$this->close();

		return $resut;
	}
}