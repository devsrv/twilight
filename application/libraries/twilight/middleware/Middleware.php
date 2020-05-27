<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Middleware {
	protected $CI;
	private array $middlewares = [];

	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->config->load('middleware', TRUE);

		$this->middlewares = $this->CI->config->item('middleware');
	}

	/**
	 * resolve middleware class by registered key from kernal file
	 * 
	 * @return application\libraries\twilight\middleware\MiddlewareInterface callable instance
	 */
	private function resolve(string $key) : MiddlewareInterface
	{
		if(! array_key_exists($key, $this->middlewares)) {
			throw new Exception('middleware name not registered, please check middleware.php config file');
		}

		$middlewareClassParts = explode('/', $this->middlewares[$key]);
		$middlewareClass = end($middlewareClassParts);
		return new $middlewareClass;
	}

	/**
	 * execute user defined middleware(s) for a middleware route match
	 */
	public static function execMiddleware($middleware)
	{
		if(is_array($middleware)) {
			foreach($middleware as $key) {
				(new self())->run($key);
			}
		}
		else {
			(new self())->run($middleware);
		}
	}

	/**
	 * call the middleware __invoke
	 */
	private function run(string $middlewareKey)
	{
		try {
			$callable = $this->resolve($middlewareKey);
			$callable();

		} catch (\Exception $e) {
			show_error($e->getMessage());
		}
	}
}
