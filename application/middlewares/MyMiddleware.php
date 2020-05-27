<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyMiddleware {
	public function __invoke($param1, $param2)
	{
		echo 'hello MyMiddleware - '.$param2.' <br/>';
	}
}
