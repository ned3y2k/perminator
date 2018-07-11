<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-09
 * Time: 오후 4:53
 */

namespace classes\initializer;

use classes\context\ApplicationContext;
use classes\test\context\TestApplicationContext;

class ContextInitializer implements Initializer {
	public function init() {
		\ApplicationContextPool::set(
			!TEST
				? new ApplicationContext()
				: new TestApplicationContext()
		);
	}
}