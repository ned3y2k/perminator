<?php
/**
 * Created by PhpStorm.
 * User: Kyeongdae
 * Date: 2018-07-17
 * Time: 오후 4:48
 */

namespace classes\web\mvc;


use classes\context\IApplicationContext;
use classes\context\RequestContext;
use classes\exception\http\HTTPResponseException;
use classes\exception\mvc\ActionControllerResponseException;
use classes\web\HttpResponse;

abstract class ActionController implements IController
{
	/** @var IApplicationContext */
	private $applicationContext;
	/** @var RequestContext */
	private $requestContext;

	/** @return string[] */
	static function requireLibs()
	{
		return array('func/request');
	}

	public function onCreate() { }

	/**
	 * @return HttpResponse
	 */
	final function handleRequest(): HttpResponse
	{
		$this->beforeHandleRequest();
		$actionName = $this->requestContext->getParam($this->getActionParamName(), null);

		return self::procControllerResult($this->doResponseDispatch($actionName));
	}

	protected function beforeHandleRequest()
	{
	}

	/** @return string 재정의 해서 action param을 바꿀수 있다. */
	protected function getActionParamName()
	{
		return 'action';
	}

	/** @return string */
	public final function getAction()
	{
		return $this->requestContext->getParam($this->getActionParamName());
	}

	/**
	 * @param string $actionName
	 * @param HttpResponse|IPageBuilder $response
	 * @return HttpResponse|IPageBuilder
	 */
	private function checkResponse($actionName, $response)
	{
		if (empty($actionName))
			$actionName = 'defaultHandle';

		if ($response == null)
			throw new HTTPResponseException($actionName . ' does not respond', 404);
		elseif (!$this->validResponse($response))
			throw new HTTPResponseException($actionName . ' invalid respond', 500);

		return $response;
	}

	/** @return IPageBuilder|HttpResponse */
	public abstract function handleDefault();

	/**
	 * 액션 url을 생성
	 * @param string $action 액션명
	 * @param array $args 옵션 인자
	 * @param string $scheme http인지 https인지
	 * @param int $port 포트
	 * @param string $relativePath 스크립 상대 경로
	 * @return string
	 */
	public final function createActionUrl($action = null, array $args = null, $scheme = null, $port = null, $relativePath = null)
	{
		if ($scheme == null)
			$scheme = $this->requestContext->getScheme();
		if ($port == null)
			$port = $_SERVER["SERVER_PORT"];
		if ($args == null)
			$args = array();
		if ($action != null)
			$args[$this->getActionParamName()] = $action;
		if ($relativePath == null) $relativePath = _SELF_;

		foreach ($args as $key => $arg) {
			if (strlen($arg) == 0) unset($args[$key]);
		}

		/** @var string $scheme */
		if ($scheme == 'http') {
			$scheme = 'http://';
			//$portAppend = $port != 80 ? ':' . $port : '';
		} elseif ($scheme == 'https') {
			$scheme = 'https://';
			// $portAppend = $port != 443 ? ':' . $port : '';
		} else {
			throw new \InvalidArgumentException('Invalid Scheme');
		}
		$portAppend = ':' . $port;

		$hostNames = explode(':', $_SERVER["HTTP_HOST"]);
		return $scheme . $hostNames[0] . $portAppend . $relativePath . ((empty($args)) ? '' : '?' . http_build_query($args));
	}

	/**
	 * @param $response
	 * @return HttpResponse
	 */
	public static function procControllerResult($response): HttpResponse
	{
		if ($response instanceof PageBuilder) {
			return $response->display();
		} elseif (is_string($response)) {
			if (strpos($response, "redirect:") === 0) {
				if (strlen($response) == 9) throw new \InvalidArgumentException("invalid redirect url");
				$newResponse = new HttpResponse();
				$newResponse->redirect(substr($response, 9));
				return $newResponse;
			} else {
				throw new ActionControllerResponseException("unknown response", 500);
			}
		} else {
			throw new ActionControllerResponseException("unknown response", 500);
		}
	}

	/** @return IApplicationContext */
	protected final function getApplicationContext()
	{
		return $this->applicationContext;
	}

	/** @param IApplicationContext $applicationContext */
	final function setApplicationContext(IApplicationContext $applicationContext)
	{
		$this->applicationContext = $applicationContext;
		$this->requestContext = $this->applicationContext->getRequestContext();
	}

	/**
	 * 접근 차단됨
	 * @return HttpResponse
	 */
	protected function showAccessDenied()
	{
		$res = new HttpResponse();
		$res->getSetting()->compress = true;
		$res->setBody('403 Forbidden');
		$res->status(403);

		return $res;
	}

	/**
	 * @param $response
	 * @return bool
	 */
	private function validResponse($response)
	{
		return
			($response instanceof HttpResponse)
			|| ($response instanceof PageBuilder)
			|| is_string($response);
	}

	/**
	 * @param $actionName
	 * @return HttpResponse|IPageBuilder
	 */
	private function doResponseDispatch($actionName)
	{
		$req = $this->applicationContext->getRequestContext();
		$reqM = $req->getRequestMethod();

		if (!$actionName) {
			$shortMethodName = 'handleDefault';
			$suffix = 'HandleDefault';
		} else {
			$shortMethodName = 'handle' . ucwords($actionName);
			$suffix = ucwords($shortMethodName);
		}

		if ($reqM->isGet() && method_exists($this, $longMethodName = 'get' . $suffix)) {
			return $this->checkResponse($actionName, $this->$longMethodName());
		} elseif ($reqM->isPost() && method_exists($this, $longMethodName = 'post' . $suffix)) {
			return $this->checkResponse($actionName, $this->$longMethodName());
		} elseif ($reqM->isDelete() && method_exists($this, $longMethodName = 'delete' . $suffix)) {
			return $this->checkResponse($actionName, $this->$longMethodName());
		} elseif ($reqM->isHead() && method_exists($this, $longMethodName = 'head' . $suffix)) {
			return $this->checkResponse($actionName, $this->$longMethodName());
		} elseif ($reqM->isConnect() && method_exists($this, $longMethodName = 'connect' . $suffix)) {
			return $this->checkResponse($actionName, $this->$longMethodName());
		} elseif ($reqM->isPut() && method_exists($this, $longMethodName = 'put' . $suffix)) {
			return $this->checkResponse($actionName, $this->$longMethodName());
		} elseif ($reqM->isOptions() && method_exists($this, $longMethodName = 'options' . $suffix)) {
			return $this->checkResponse($actionName, $this->$longMethodName());
		} elseif (method_exists($this, $shortMethodName)) {
			return $this->checkResponse($actionName, $this->$shortMethodName());
		} else {
			throw new HTTPResponseException($actionName . ' action not found', 404);
		}
	}
}