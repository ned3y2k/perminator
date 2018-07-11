<?php
namespace classes\builder\html;

use classes\model\html\form\select\{
	Option, SelectElement
};

class SelectBuilderException extends \RuntimeException {}
class SelectBuilder {
	private $valueList;
	private $textList;
	private $selectedValued;

	/**
	 * @param string[] $valueList
	 *
	 * @return SelectBuilder
	 */
	public function setValueList(array $valueList) { $this->valueList = $valueList; return $this; }

	/**
	 * @param string[] $textList
	 *
	 * @return SelectBuilder
	 */
	public function setTextList(array $textList) { $this->textList = $textList; return $this; }

	/**
	 * @param string $value
	 *
	 * @return SelectBuilder
	 */
	public function setSelectedValues($value) { $this->selectedValued = $value; return $this; }

	/**
	 * @throws SelectBuilderException
	 * @return SelectElement
	 */
	public function build() {
		$itemCount = count($this->valueList);
		if($itemCount != count($this->textList)) throw new SelectBuilderException("Text and data are not paired.");

		$selectElement = new SelectElement();
		for($i = 0; $i < $itemCount; $i ++) {
			$option = new Option();

			$option->setAttribute('value', $this->valueList[$i]);
			$option->setTextNode($this->textList[$i]);
			$option->setSeleted($this->selectedValued == $this->valueList[$i] ? true : false);

			$selectElement->appendChild($option);
		}

		return $selectElement;
	}
}