<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/twilight/middleware/Route.php';

/**
 * route & middleware mapping
 */
class ApplyMiddleware {
	/**
	 * register route with middleware alias
	 */
	public static function register()
	{
		Route::is('welcome')->apply('test.middleware');

		Route::is('welcome/index')->apply(['test.middleware', 'my.middleware']);
	}
}
