<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ShouldAuth implements MiddlewareInterface {

	public function __construct() {
		require_once APPPATH.'/libraries/twilight/authenticator/Auth.php';
	}

	public function __invoke(...$params)
	{
		if(! (new Auth)->check()) {
			show_error('not allowed, only for authenticated users', 401);
		}

		return TRUE;
	}
}
