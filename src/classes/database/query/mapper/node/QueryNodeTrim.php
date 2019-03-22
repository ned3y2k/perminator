<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 15
 * 시간: 오전 12:07
 */
namespace classes\database\query\mapper\node;
use classes\database\conf\mapper\DynamicQueryConst;
use classes\lang\ArrayUtil;

/**
 * prefix 처리 후 엘리먼트의 내용이 있으면 가장 앞에 붙여준다.
 * prefixOverrides 처리 후 엘리먼트 내용 중 가장 앞에 해당 문자들이 있다면 자동으로 지워준다.
 * suffix 처리 후 엘리먼트 내에 내용이 있으면 가장 뒤에 붙여준다.
 * suffixOverrides 처리 후 엘리먼트 내용중 가장 뒤에 해당 문자들이 있다면 자동으로 지워준다.
 */

class QueryNodeTrim extends AbstractChildNode {
    /** @var string 머리에 붙일 문자열 */
    private $prefix;
    /** @var string 머리에서 제거할 문자열 */
    private $prefixOverrides;

    /** @var string 꼬리에 붙일 문자열 */
    private $suffix;
    /** @var string 꼬리에서 제거할 문자열 */
    private $suffixOverrides;

	/**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->prefix = ArrayUtil::getValue($attributes, DynamicQueryConst::PREFIX, '');
        $this->suffix = ArrayUtil::getValue($attributes, DynamicQueryConst::SUFFIX, '');;

        $val = ArrayUtil::getValue($attributes, DynamicQueryConst::PREFIX_OVERRIDES);
        if($val !== null && strlen($val) > 0) $this->prefixOverrides = explode('|',$val);

        $val = ArrayUtil::getValue($attributes, DynamicQueryConst::SUFFIX_OVERRIDES);
        if($val !== null && strlen($val) > 0) $this->suffixOverrides = explode('|',$val);
    }

	/** @return string */
    public function __toString()
    {
        $buff = parent::__toString();
        $buff = $this->performSuffixOverrides($buff);
        $buff = $this->performPrefixOverrides($buff);

        if(trim($buff)) {
            if(strlen($buff) != 0) {
                $buff = $this->prefix.' '.$buff;
                $buff .= ' '.$this->suffix;
            }
            return $buff;
        }

        return '';
    }

	/**
     * @param string $buff
     * @return string
     */
    private function performPrefixOverrides($buff)
    {
        if(is_array($this->prefixOverrides)) foreach($this->prefixOverrides as $prefixOverride) {
            $prefixOverride = trim($prefixOverride);
            if(strlen($prefixOverride) == 0) continue;

            $strLen = strlen($prefixOverride);
            $buff = trim($buff);
            if(substr($buff, 0, $strLen) == $prefixOverride) {
                $buff = substr($buff, $strLen);
            }
        }

        return $buff;
    }

	/**
     * @param string $buff
     * @return string
     */
    private function performSuffixOverrides($buff)
    {
        if(is_array($this->suffixOverrides)) foreach($this->suffixOverrides as $suffixOverride) {
            $suffixOverride = trim($suffixOverride);

            if(strlen($suffixOverride) == 0) continue;

            $strLen = strlen($suffixOverride);
            $buff = trim($buff);

            if(substr($buff, -$strLen) == $suffixOverride) {
             $buff = substr($buff, 0, strlen($buff)-$strLen);
            }
        }

        return $buff;
    }
} 