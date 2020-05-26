<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface MiddlewareInterface
{
    /**
     * Middleware entry point
     * 
     * @return mixed
     */
    public static function handle();
}
