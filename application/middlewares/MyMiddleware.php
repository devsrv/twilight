<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyMiddleware implements MiddlewareInterface {
	public function __invoke()
	{
		echo 'hello MyMiddleware <br/>';
	}
}
