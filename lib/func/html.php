<?php
use classes\lang\StringBuilder;
/**
 * 메시지를 출력하고 스크립트로 이전 페이지로...
 * @param string $msg 출력할 메시지
 * @param bool $isExit 메시지를 출력하고 스크립트를 종료할지 여부(기본 false)
 */
function print_alert_location_back($msg, $isExit = false) {
	header('content-type: text/html; charset=utf-8');
	echo "<script type='text/javascript'>alert('".addslashes($msg)."');history.back();</script>";
	if($isExit) exit;
}


/**
 * select에 사용하는 option들을 표시
 * @param string[] $valueList 실제값 목록
 * @param string[] $nameList 보여질 이름 목록
 * @param string $selectedValue 선택된 값
 * @throws InvalidArgumentException
 */
function html_option($valueList, $nameList, $selectedValue) {
	$strBuilder = new StringBuilder();

	$valueCount = count($valueList);
	if(count($valueList) != count($nameList)) throw new InvalidArgumentException('Value and Name are not paired.');

	for ($i = 0; $i < $valueCount; $i++) {
		if($selectedValue == $valueList[$i]) $strBuilder->append("<option value='{$valueList[$i]}' selected='selected'>{$nameList[$i]}</option>");
		else $strBuilder->append("<option value='{$valueList[$i]}'>{$nameList[$i]}</option>");
	}

	return $strBuilder->toString();
}

/**
 * checkbox들을 표시한다.
 * <label><input name='{$checkboxName}' type='checkbox' value='{$valueList[$i]}' />{$nameList[$i]}</label> 형식에 따른다.
 * @param unknown $checkboxName
 * @param unknown $valueList
 * @param unknown $nameList
 * @param array $selectedValues
 * @throws InvalidArgumentException
 */
function html_checkbox($checkboxName, $valueList, $nameList, array $selectedValues = null) {
	if(is_null($selectedValues)) $selectedValues = array();

	$valueCount = count($valueList);
	if(count($valueList) != count($nameList)) throw new InvalidArgumentException('Value and Name are not paired.');

	for ($i = 0; $i < $valueCount; $i++) {
		if(in_array($valueList[$i], $selectedValues)) return "<label><input name='{$checkboxName}' type='checkbox' value='{$valueList[$i]}' checked='checked' />{$nameList[$i]}</label>";
		return "<label><input name='{$checkboxName}' type='checkbox' value='{$valueList[$i]}' />{$nameList[$i]}</label>";
	}
}

/**
 * html에서 인라인 스타일을 제거한다.
 * @param string $text html
 * @return string
 */
function html_remove_inline_attr($text) { return preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', $text); }

/**
 * input, radio 에서 사용
 * @param string $expected 예상되는 값
 * @param mixed $actual 실제 값
 * @return string
 *
 * @tutorial
 * <code>
 * $expected = 'b';
 * $actual = array('a','c','d');
 *
 * print_html_attr_checked($expected, $actual); // return is Empty String
 *
 * $expected = 'b';
 * $actual = 'b'
 *
 * print_html_attr_checked($expected, $actual); // return is checked='checked'
 * </code>
 */
function html_attr_checked($expected, $actual) {
	if(is_array($actual)) return in_array($expected, $actual) ? "checked='checked'" :'';
	else return $expected == $actual ? "checked='checked'" :'';
}

/**
 * select 에서 사용
 * @param string $expected 예상되는 값
 * @param mixed $actual 실제 값
 * @return string
 *
 * @tutorial
 * <code>
 * $expected = 'b';
 * $actual = array('a','c','d');
 *
 * print_html_attr_checked($expected, $actual); // return is Empty String
 *
 * $expected = 'b';
 * $actual = 'b'
 *
 * print_html_attr_checked($expected, $actual); // return is checked='checked'
 * </code>
 */
function html_attr_selected($expected, $actual) {
	if(is_array($actual)) return in_array($expected, $actual) ? "selected='selected'" :'';
	else return $expected == $actual ? "selected='selected'" :'';
}

/**
 * 24시간을 표시
 * @param int $hour 선택된 시간
 */
function html_option_hour($hour) {
	$hour = intval($hour);
	$temp = array();

	for ($i=0;$i<24;$i++) {
		$str = sprintf('%02d', $i);
		$temp[] =  $i ? "<option value='{$str}' selected='selected'>{$i}</option>" : "<option value='{$str}'>{$i}</option>";
	}

	return implode('', $temp);
}

/**
 * 분, 초를 표시
 * @param int $val 선택된 시간
 */
function html_option_min_sec($val) {
	$val = intval($val);
	$temp = array();

	for ($i=0;$i<60;$i++) {
		$str = sprintf('%02d', $i);
		$temp[] = $val == $i ? "<option value='{$str}' selected='selected'>{$i}</option>" : "<option value='{$str}'>{$i}</option>";
	}

	return implode('', $temp);
}



/**
 * option element를 생성
 * @todo 개선 하여야함
 * @param array $itemList
 * @param mixed $selectItem
 * @param callable[] $callbacks
 *        	getValue($item), getName($item)
 */
function make_print_option(array $itemList, $selectItem, array $callbacks = null) {
	$optionElements = array ();

	$selectedFormat = '<option value="%s" selected="selected">%s</option>';
	$format = '<option value="%s">%s</option>';

	$endItem = end ( $itemList );

	if (is_scalar ( $endItem )) {
		foreach ( $itemList as $item ) {
			$optionElement = $item == $selectItem ? sprintf ( $selectedFormat, $item, $item ) : sprintf ( $selectedFormat, $item, $item );
			array_push ( $optionElements, $optionElement );
		}
	} elseif (is_array ( $endItem )) {
		if (is_null ( $callbacks )) {
			foreach ( $itemList as $item ) {
				$optionElement = $item == $selectItem ? sprintf ( $selectedFormat, $item [0], $item [1] ) : sprintf ( $selectedFormat, $item [0], $item [1] );
				array_push ( $optionElements, $optionElement );
			}
		} else {
			foreach ( $itemList as $item ) {
				$name = call_user_func ( $callbacks ['getName'], $item );
				$value = call_user_func ( $callbacks ['getValue'], $item );
				$currentValue = call_user_func ( $callbacks ['getValue'], $selectItem );

				$optionElement = $value == $currentValue ? sprintf ( $selectedFormat, $value, $name ) : sprintf ( $format, $value, $name );
				array_push ( $optionElements, $optionElement );
			}
		}
	} elseif (is_object ( $endItem )) {
		foreach ( $itemList as $item ) {
			$name = call_user_func ( $callbacks ['getName'], $item );
			$value = call_user_func ( $callbacks ['getValue'], $item );
			$currentValue = call_user_func ( $callbacks ['getValue'], $selectItem );

			$optionElement = $value == $currentValue ? sprintf ( $selectedFormat, $value, $name ) : sprintf ( $format, $value, $name );
			array_push ( $optionElements, $optionElement );
		}
	}

	echo implode ( "\n", $optionElements );
}