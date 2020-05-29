<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestMiddleware implements MiddlewareInterface {
	public function __invoke(...$params)
	{
		echo 'hello TestMiddleware - '.$params[0].' <br/>';
	}
}
