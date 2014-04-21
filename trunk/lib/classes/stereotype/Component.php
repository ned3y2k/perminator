<?php
namespace classes\stereotype;

use classes\context\Context;
interface Component {
	public function setContext(Context $context);
}