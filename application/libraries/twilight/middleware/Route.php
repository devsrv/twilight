<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Route {
	protected $CI;

	private bool $routeMatch = FALSE;

    private static array $placeholderReplacements = [
        '/\(:string\)/'  => '[a-zA-Z]+',
        '/\(:num\)/'  => '[0-9]+',
    ];

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	/**
	 * find exact match for the given path with the uri
	 */
	public static function is(string $route) : self
	{
		$self = new self();
		if($self->CI->uri->uri_string() === $route) {
			$self->routeMatch = TRUE;
		}

		return $self;
	}

	/**
	 * find segment match
	 */
	public static function segment(int $n, string $segment) : self
	{
		$self = new self();
		if($self->CI->uri->segment($n) === $segment) {
			$self->routeMatch = TRUE;
		}

		return $self;
	}

	/**
	 * find regex segment match
	 */
	public static function match(string $pattern) : self
	{
		$pattern = str_replace('/', '\/', $pattern);
		$pattern = preg_replace(array_keys(self::$placeholderReplacements), array_values(self::$placeholderReplacements), $pattern,1);

		$self = new self();
		if(preg_match('/'. $pattern .'/', $self->CI->uri->uri_string())) {
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
			Middleware::execMiddleware($middleware);
		}
	}
}
