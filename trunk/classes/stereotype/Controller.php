<?php
namespace classes\stereotype;

use classes\content\Context;

interface Controller {
	public function setContext(Context $context);
}