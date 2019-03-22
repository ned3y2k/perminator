<?php
namespace classes\exception;

class UserDebugException extends \RuntimeException {
	/**
	 * @param string $message,...
	 */
	function __construct($message) {
		foreach(func_get_args() as $arg) {
			if(empty($this->message))
				$this->message .= $this->inflateMessage($arg);
			else
				$this->message .= "\n------ Next ------\n".$this->inflateMessage($arg);
		}
	}

	/**
	 * @param mixed $message
	 * @return string
	 */
	private function inflateMessage($message) {
		if ($message === null)
			return '';
		elseif (is_bool($message))
			return $message ? 'true' : 'false';
		elseif (is_scalar($message))
			return $message;
		else
			return var_export($message, true);
	}
}