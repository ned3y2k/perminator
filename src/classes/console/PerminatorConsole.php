<?php
/**
 * User: Kyeongdae
 * Date: 2019-03-14
 * Time: 오전 3:20
 */

namespace classes\console;


use classes\console\command\InitCommand;
use classes\context\ConsoleApplicationContext;
use Symfony\Component\Console\Application;

class PerminatorConsole  extends Application {
	public function __construct(string $name = 'UNKNOWN', string $version = 'UNKNOWN') {
		parent::__construct($name, $version);

		\ApplicationContextPool::set(new ConsoleApplicationContext());
		$this->add(new InitCommand());
	}
}