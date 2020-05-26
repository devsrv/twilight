<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Route {
	protected $CI;
	private array $middlewares = [];
	private bool $routeMatch = FALSE;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->middlewares = Kernel::getRegisteredMiddlewares();
	}

	/**
	 * find exact match for the given path with the uri
	 */
	public static function is(string $route) : self
	{
		$self = new self();
		if(! $self->routeMatch && $self->CI->uri->uri_string() === $route) {
			$self->routeMatch = TRUE;
		}

		return $self;
	}

	/**
	 * execute the middlewares for that route
	 */
	public function apply($middleware) : void
	{
		if($this->routeMatch === TRUE)
		{
			if(is_array($middleware)) {
				foreach($middleware as $key) {
					$class = $this->middlewares[$key];
					$class::handle();
				}
			}
			else {
				$class = $this->middlewares[$middleware];
				$class::handle();
			}
		}
	}
}
