<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Middleware {
	protected $CI;
	private array $middlewares = [];

	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->config->load('middleware', TRUE);

		$middleware_config = $this->CI->config->item('middleware');
		$this->middlewares = $middleware_config['alias'];
	}

	/**
	 * resolve middleware class by registered key from kernal file
	 */
	private function resolve(string $key) : void
	{
		if(strpos($key, ':')) {
			[$middlewareKey, $params] = explode(':', $key);
		}
		else {
			$middlewareKey = $key;
			$params = NULL;
		}

		if(! array_key_exists($middlewareKey, $this->middlewares)) {
			throw new Exception('middleware name not registered, please check middleware.php config file');
		}

		$middlewareClassParts = explode('/', $this->middlewares[$middlewareKey]);
		$middlewareClass = end($middlewareClassParts);
		$callable = new $middlewareClass;

		if(is_callable($callable)) {
			if($params !== NULL) {
				call_user_func_array($callable, explode(',', $params));
			}
			else {
				$callable();
			}
		}
		else {
			throw new Exception('middleware not executable');
		}
		
	}

	/**
	 * execute user defined middleware(s) for a middleware route match
	 */
	public static function execMiddleware($middleware): void
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
	private function run(string $middlewareKey): void
	{
		try {
			$this->resolve($middlewareKey);

		} catch (\Exception $e) {
			show_error($e->getMessage());
		}
	}
}
