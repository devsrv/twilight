<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyMiddleware implements MiddlewareInterface {
	public function __invoke(...$params)
	{
		echo 'hello MyMiddleware - '.$params[1].' <br/>';
	}
}
