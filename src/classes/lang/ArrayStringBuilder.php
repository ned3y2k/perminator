<?php

namespace classes\lang;

/**
 * @author 경대
 * Perminator Class
 */
class ArrayStringBuilder extends UniqueObject implements IStringBuilder {
	/** @var string[] 임시저장 버퍼 */
	private $buffer = array();

	/**
	 * @param string $str
	 *
	 * @return IStringBuilder
	 * @throws \InvalidArgumentException
	 */
	public function append($str) {
		if (!is_string($str) && $str instanceof self) throw new \InvalidArgumentException();
		$this->buffer [ ] = $str;
		StringPool::getInstance()->delete($this);
		return $this;
	}

	/**
	 * @return string
	 */
	public function toString(): string {
		$string = StringPool::getInstance()->get($this);
		if($string == null) {
			$string = implode($this->buffer);
			StringPool::getInstance()->put($string, $this);
		}

		return $string;
	}

	/** @return string */
	public function __toString(): string {
		return $this->toString();
	}
}