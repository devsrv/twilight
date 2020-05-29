<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface MiddlewareInterface {
	public function __invoke(...$params);
}
