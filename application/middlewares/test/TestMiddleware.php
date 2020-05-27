<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestMiddleware {
	public function __invoke($param)
	{
		echo 'hello TestMiddleware - '.$param.' <br/>';
	}
}
