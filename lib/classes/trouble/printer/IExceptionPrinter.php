<?php

namespace classes\trouble\printer;

interface IExceptionPrinter {
	public function publish(\Exception $exception);
}