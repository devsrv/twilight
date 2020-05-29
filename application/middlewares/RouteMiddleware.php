<?php
defined('BASEPATH') OR exit('No direct script access allowed');



// Route::is('')->apply('test.middleware');
// Route::match('(:string)')->apply(['test.middleware:rav', 'my.middleware:sourav, rakshit']);

// Route::match('welcome/(:string)/(:num)')->apply('test.middleware:rav');

// Route::is('welcome')->apply('test.middleware');

Route::is('login')->apply('guest');
Route::is('logout')->apply('auth');
Route::is('dashboard')->apply('auth');

// Route::is('welcome/index')->apply(['test.middleware', 'my.middleware']);

// Route::segment(1, 'welcome')->apply(['test.middleware']);
