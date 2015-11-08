<?php
namespace classes\stereotype;

use classes\context\Context;
use classes\web\bind\meta\BeanAttributeContainer;

interface Component {
	public function setContext(Context $context);
}
interface Controller extends Component {}
interface AutowiredBeansOwnedController extends Controller {
	/**
	 * @param Context $context
	 * @return BeanAttributeContainer
	 */
	public static function createAutowiredBeanMap(Context $context);
}
interface RequestMapOwnedController extends Controller {
	/**
	 * FIXME reuqest map container 도 만들것!
	 *
	 * @param Context $context
	 * @return
	 */
	public static function createRequestMap(Context $context);
}

interface Repository extends Component {}
interface Service extends Component {}
interface BeanFactory extends Component {}