<?php
namespace classes\stereotype;

interface Component {
	public function setContext(\classes\context\Context $context);
}
interface Controller extends Component {}
interface AutowiredBeansOwnedController extends Controller {
	public static function createAutowiredBeanMap(\classes\context\Context $context);
}
interface RequestMapOwnedController extends Controller {
	public static function createRequestMap(\classes\context\Context $context);
}

interface Repository extends Component {}
interface Service extends Component {}
interface BeanFactory extends Component {}