<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * route & middleware mapping
 */
class ApplyMiddleware extends MiddlewareResolver {
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * register route with middleware alias
	 */
	public function register()
	{
		require_once APPPATH . '/'. parent::$middleware_map_file . '.php';
	}
}
