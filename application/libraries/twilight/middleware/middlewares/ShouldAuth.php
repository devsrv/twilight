<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/libraries/twilight/middleware/middlewares/Authsupport.php';

class ShouldAuth extends Authsupport implements MiddlewareInterface {
	public function __construct() {
		parent::__construct();
	}

	public function __invoke(...$params)
	{
		if(! (new Auth)->check()) {
			// show_error('not allowed, only for authenticated users', 401);
			parent::redirectTo();
		}
	}
}
