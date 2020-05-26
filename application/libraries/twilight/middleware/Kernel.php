<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kernel {
	/**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        'TestMiddleware',
		'MyMiddleware'
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
			'TestMiddleware',
			'MyMiddleware'
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    private static $routeMiddleware = [
		'test.middleware' => 'middlewares/test/TestMiddleware',
		'my.middleware' => 'middlewares/MyMiddleware',
	];
	
    public static function getRegisteredMiddlewares() : array {
		return self::$routeMiddleware;
	}
}
