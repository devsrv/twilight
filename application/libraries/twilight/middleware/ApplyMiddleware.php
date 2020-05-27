<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * route & middleware mapping
 */
class ApplyMiddleware {
	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->config->load('middleware', TRUE);
	}

	/**
	 * register route with middleware alias
	 */
	public function register()
	{
		$middleware_config = $this->CI->config->item('middleware');

		if(! isset($middleware_config['__config']['middleware_map_file'])) {
			throw new Exception('middleware_map_file configuration missing');
		}
		else if(! file_exists(APPPATH . '/'. $middleware_config['__config']['middleware_map_file'] . '.php')) {
			throw new Exception("can't load middleware_map_file");
		}
		else {
			require_once APPPATH . '/'. $middleware_config['__config']['middleware_map_file'] . '.php';
		}
	}
}
