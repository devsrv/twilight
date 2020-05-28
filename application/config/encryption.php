<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Default Hash Driver
|--------------------------------------------------------------------------
|
| This option controls the default hash driver that will be used to hash
| passwords for your application. By default, the bcrypt algorithm is
| used; however, you remain free to modify this option if you wish.
|
| Supported: "bcrypt", "argon", "argon2id"
|
*/

$config['hash']['algorithm'] = 'bcrypt';

/*
|--------------------------------------------------------------------------
| Bcrypt Options
|--------------------------------------------------------------------------
|
| Here you may specify the configuration options that should be used when
| passwords are hashed using the Bcrypt algorithm. This will allow you
| to control the amount of time it takes to hash the given password.
|
*/

$config['hash']['bcrypt'] = [
	'cost' => 10,
];

/*
|--------------------------------------------------------------------------
| Argon Options
|--------------------------------------------------------------------------
|
| Here you may specify the configuration options that should be used when
| passwords are hashed using the Argon algorithm. These will allow you
| to control the amount of time it takes to hash the given password.
|
*/

$config['hash']['argon'] = [
	'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
	'time_cost' => PASSWORD_ARGON2_DEFAULT_TIME_COST,
	'threads' => PASSWORD_ARGON2_DEFAULT_THREADS,
];
