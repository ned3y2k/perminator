<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 14
 * 시간: 오후 9:53
 */

namespace classes\database\query\mapper\node;


use classes\database\query\mapper\DynamicQueryContext;
use classes\database\query\mapper\exception\DynamicQueryBuilderException;

abstract class AbstractChildNode implements IQueryNode {
    /** @var IQueryNode[] */
    protected $childNodes = array();
    /** @var DynamicQueryContext */
    protected $context;

    public function setAttributes(array $attributes) { throw new DynamicQueryBuilderException(get_called_class().' not implemented setAttributes'); }

	/**
	 * CDATA 를 넣을때 사용
	 * @param $text
	 */
    public function setText($text) { throw new DynamicQueryBuilderException(get_called_class().' not implemented setText'); }

    public function __toString() {
        $buff = '';
        foreach($this->childNodes as $childNode) {
	        $tmp = $childNode->__toString();

	        if(strlen($tmp) > 0)
		        $buff .=  $tmp;
        }

        return $buff;
    }

    public final function addNode(IQueryNode $node) { $this->childNodes[] = $node; }

    public final function getChildNodes() { return $this->childNodes; }

    public final function setContext(DynamicQueryContext $context = null) {
        $this->context = $context;
        foreach($this->childNodes as $childNode) {
            $childNode->setContext($context);
        }
    }

    public function nodeName() {
	    return strtolower(str_replace('classes\database\query\mapper\node\QueryNode', "", get_class($this)));
    }
}