<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2019-02-24
 * Time: 오후 5:35
 */

namespace classes\web\dispatch\controller\error;


use classes\context\ApplicationContext;
use classes\context\IApplicationContext;
use classes\web\mvc\IController;
use classes\web\response\HttpResponse;

class ErrorController implements IController {

	/**
	 * @var \Throwable
	 */
	private $ex;
	private $statusCode;
	/** @var ApplicationContext */
	private $applicationContext;
	/** @var ErrorResponseCreator */
	private $errorPrinter;

	public function __construct(\Throwable $ex, int $statusCode = 500) {
		$this->ex         = $ex;
		$this->statusCode = $statusCode;
		$this->errorPrinter = new ErrorResponseCreator();
	}

	/**
	 * @param IApplicationContext $applicationContext
	 *
	 * @return void
	 */
	function setApplicationContext(IApplicationContext $applicationContext) {
		$this->applicationContext = $applicationContext;
	}

	/** @return HttpResponse */
	function handleRequest(): HttpResponse {
		$creator = new ErrorResponseCreator();
		return $creator->create($this->applicationContext, $this->ex, $this->statusCode);
	}

	function onCreate() {
		// TODO: Implement onCreate() method.
	}
}