<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH.'/twilight/middleware/Kernel.php';
require_once APPPATH.'/twilight/middleware/ApplyMiddleware.php';
require_once APPPATH.'/twilight/middleware/MiddlewareInterface.php';

/**
 * Middleware support class
 *
 * @author Sourav R <developer.srv1@gmail.com>
 */
class MiddlewareProvider {
	protected $CI;
	private $middlewares = [];

	public function __construct()
	{
		$this->CI =& get_instance();

		$this->middlewares = Kernel::getRegisteredMiddlewares();

		$this->loadMiddlewares();
	}

	/**
	 * register the middleware names with their class
	 */
	public function Register() : void
	{
		ApplyMiddleware::register();
	}

	/**
	 * load all registered middlewares
	 */
	private function loadMiddlewares() : void
	{
		foreach($this->middlewares as $name => $middlewareFile) {
			$this->load($middlewareFile);
		}
	}

	/**
     * Loads a middleware class
     * 
     * @param mixed $middleware Middleware name
     */
    public static function load(String $middleware) : ? bool
    {
        $target = APPPATH . '/twilight/middleware/' . $middleware . '.php';

        if( file_exists($target))
        {
            require_once($target);

            $middlewareInstance = new $middleware();

            if(!$middlewareInstance instanceof MiddlewareInterface)
            {
                show_error('Your middleware MUST implement the "MiddlewareInterface" interface');
			}
			
			return TRUE;
        }

        show_error('Unable to find <strong>' . $middleware .'.php</strong> in your application/twilight/middleware folder');
    }
}
