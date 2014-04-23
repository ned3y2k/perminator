<?php
namespace classes\stereotype;

interface Component {
	public function setContext(\classes\context\Context $context);
}
interface Controller extends Component {}
interface AutowiredBeansOwnedController extends Controller {
	/**
	 * @param \classes\context\Context $context
	 * @return \classes\web\bind\meta\BeanAttributeContainer
	 */
	public static function createAutowiredBeanMap(\classes\context\Context $context);
}
interface RequestMapOwnedController extends Controller {
	/**
	 * @param \classes\context\Context $context
	 * @return FIXME reuqest map container 도 만들것!
	 */
	public static function createRequestMap(\classes\context\Context $context);
}

interface Repository extends Component {}
interface Service extends Component {}
interface BeanFactory extends Component {}