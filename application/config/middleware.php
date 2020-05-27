<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['__config']['middleware_map_file'] = 'middlewares/RouteMiddleware';

$config['alias']['test.middleware'] = 'middlewares/test/TestMiddleware';
$config['alias']['my.middleware'] = 'middlewares/MyMiddleware';
