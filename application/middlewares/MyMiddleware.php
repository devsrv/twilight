<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MyMiddleware implements MiddlewareInterface {
	public static function handle()
	{
		echo 'hello MyMiddleware <br/>';
	}
}
