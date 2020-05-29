<?php
defined('BASEPATH') OR exit('No direct script access allowed');

spl_autoload_register(fn ($class) => require_once APPPATH.'/libraries/twilight/middleware/' . $class . '.php');

/**
 * Middleware support class
 *
 * @author Sourav R <developer.srv1@gmail.com>
 */
class MiddlewareProvider extends MiddlewareResolver {
	public function __construct()
	{
		parent::__construct();

		$this->loadMiddlewares();
	}

	/**
	 * register the middleware names with their class
	 */
	public function Register() : void
	{
		(new ApplyMiddleware())->register();
	}

	/**
	 * load all registered middlewares
	 */
	private function loadMiddlewares() : void
	{
		foreach(parent::$middlewares as $name => $middlewareFile) {
			try {
				$this->load($middlewareFile);
			} catch (\Exception $e) {
				show_error($e->getMessage());
			}
			
		}
	}

	/**
     * Loads a middleware class
     * 
     * @param mixed $middleware Middleware name
     */
    public static function load(String $middleware) : ? bool
    {
		$target = APPPATH . '/' . $middleware . '.php';

        if( file_exists($target))
        {
			require_once($target);

			$middlewareClassParts = explode('/', $middleware);
			$middlewareClass = end($middlewareClassParts);
            $middlewareInstance = new $middlewareClass();

            if(! $middlewareInstance instanceof MiddlewareInterface)
            {
				throw new Exception('Your middleware MUST implement MiddlewareInterface interface');
			}

			return TRUE;
		}
		
		throw new Exception('Unable to find middleware ' . $middleware .'.php');
    }
}
