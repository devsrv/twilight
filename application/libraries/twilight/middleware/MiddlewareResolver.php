<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MiddlewareResolver {
	protected $CI;
	public static array $middlewares = [];
	public static string $middleware_map_file;

	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->config->load('middleware', TRUE);

		$middleware_config = $this->CI->config->item('middleware');

		self::$middlewares = $middleware_config['alias'];

		if(! isset($middleware_config['__config']['middleware_map_file'])) {
			throw new Exception('middleware_map_file configuration missing');
		}
		else if(! file_exists(APPPATH . '/'. $middleware_config['__config']['middleware_map_file'] . '.php')) {
			throw new Exception("can't load middleware_map_file");
		}
		else {
			self::$middleware_map_file = $middleware_config['__config']['middleware_map_file'];
		}
	}
}
