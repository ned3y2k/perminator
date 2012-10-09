<?php
namespace classes\web\bind\meta;

interface RequestParamCollection {
	/**
	 * @return RequestParam[]
	 */
	public function& getRequestParams();
}

?>