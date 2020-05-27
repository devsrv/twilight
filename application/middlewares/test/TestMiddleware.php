<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestMiddleware implements MiddlewareInterface {
	public static function handle()
	{
		echo 'hello TestMiddleware <br/>';
	}
}
