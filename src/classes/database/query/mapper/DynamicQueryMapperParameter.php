<?php
/**
 * Bitmobile System Corp.
 * 작성자: Kyeongdae
 * 일자: 2015-05-01
 * 시간: 오후 3:00
 */

namespace classes\database\query\mapper;


use classes\database\query\mapper\exception\DynamicQueryMapperParameterDuplicatedException;
use classes\database\query\mapper\exception\DynamicQueryMapperParameterOutOfBoundsException;

/**
 * Class DynamicQueryMapperParameter
 *
 * @package classes\database\query\mapper
 */
class DynamicQueryMapperParameter {
	private $nameLength;
	private $positions = array();
	private $count = 0;
	private $type;

	public function __construct($name, $type, $position) {
		$this->positions[] = $position;
		$this->nameLength = strlen($name);
		$this->count++;
		$this->type = $type;
	}

	public function getType() { return $this->type; }

	public function getLength() { return $this->nameLength; }

	/**
	 * @param $index
	 * @return mixed
	 * @throws DynamicQueryMapperParameterOutOfBoundsException
	 */
	public function getPosition($index) {
		if(array_key_exists($index, $this->positions)) return $this->positions[$index];
		throw new DynamicQueryMapperParameterOutOfBoundsException();
	}

	public function getPositions() { return $this->positions; }
	public function getCount() { return $this->count; }
	public function addPosition($position) {
		if(in_array($position, $this->positions)) throw new DynamicQueryMapperParameterDuplicatedException();
		$this->positions[] = $position;
	}
}