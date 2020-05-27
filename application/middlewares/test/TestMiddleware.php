<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestMiddleware implements MiddlewareInterface {
	public function __invoke()
	{
		echo 'hello TestMiddleware <br/>';
	}
}
