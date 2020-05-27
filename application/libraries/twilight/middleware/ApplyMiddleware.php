<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * route & middleware mapping
 */
class ApplyMiddleware {
	/**
	 * register route with middleware alias
	 */
	public static function register()
	{
		require_once 'BindRouteMiddleware.php';
	}
}
