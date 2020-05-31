# Twilight

A micro package for [CodeIgniter 3](https://codeigniter.com/userguide3/index.html) which provides useful libraries, extensions & functionalities to your application

## 📋 Features

- **Middleware**
- **Migration**
- **Hashing**
- **Complete Authentication System**
- **Two step Authentication**
- **Authorization**
- **Event & Listeners**


## 📥  Installation

The installation is made such that any fresh & existing application can easily adapt:
> for many CodeIgniter user it is troublesome to use composer in their application, so to make the package suit any CI3 application I made the installation process manual 

#### ✔ Copy & place in proper directory

1. download the `application/libraries/twilight` directory and place in your `application/libraries`
2. download the `application/config/encryption.php` and `application/config/middleware.php` and place in your `application/config`
3. similarly place `alication/middlewares/RouteMiddleware.php` to your application
4. to understand how middleware classes are written you can copy the `application/middlewares` directory and place in the same location in your app

#### ✔ Application config
1. generate a secure encryption key for your application by
```php
$this->load->library('encryption');
$key = bin2hex($this->encryption->create_key(16));

//in application/config.php
$config['encryption_key'] = hex2bin(<your hex-encoded key>);
```

2. in `config/hooks.php`
```php
$hook['post_controller_constructor'] = [
	'class'    => 'MiddlewareProvider',
	'function' => 'Register',
	'filename' => 'MiddlewareProvider.php',
	'filepath' => 'libraries/twilight/hooks',
	'params'   => NULL
];
```

3. if you want to keep the `RouteMiddleware.php` route to middleware mapping file in a different place than default `application/middlewares` then set it in `config/middleware.php`
```php
$config['__config']['middleware_map_file'] = '<YOUR PATH>/RouteMiddleware';
```

4. enable migration in `config/migration.php`
```php
$config['migration_enabled'] = TRUE;
```


```php
return [
	// the number of seconds between ajax hits to check auth session
    'gap_seconds' => 30,
    
    // whether using broadcasting feature to make the modal disappear faster
    'avail_broadcasting' => false,
```


## ⚗️ How to Use

#### 🧰 Middleware

> Middleware provide a convenient mechanism for filtering HTTP requests entering your application. 
For example, Twilight includes two middlewares (auth, guest) that verifies the user of your application is authenticated. 
If the user is not authenticated, the auth middleware will redirect the user to the login screen. However, 
if the user is authenticated, the middleware will redirect the user to the dashboard screen. Similarly guest middlewar
only allows to enter the http request if no user is currently logged in


- **Write middleware**
1. your middleware class must implement `MiddlewareInterface` which comes with Twilight & autoloaded for you no need to require / include the interface
2. you can place your middleware in any directory you prefer but remember to register it in the `config/middleware.php` `alias` group
 ```php
$config['alias']['test.middleware'] = 'path/to/YourMiddleWareClass';
```

3. the `middlewares/RouteMiddleware.php` file contains mapping rule for route to middleware(s) allocation map, make sure the path to the file is properly set in `config/middleware.php`
```php
$config['__config']['middleware_map_file'] = //set RouteMiddleware path
```

- **Apply Middleware**

there are two ways to apply middleware -
1. you can apply one or multiple middlewares to a specific route
2. you can apply middleware in your controllers / any class or function that support library loading

##### *✔ Apply to route*_________________

the `middlewares/RouteMiddleware.php` file act as mapping for URI & middleware, you can use different static method on the `Route` class (autoloaded) to match your desired URI and apply one or more middleware to that match.

| Method | Description | Example |
|-|:-:|-|
|is|matches exact string with the URI| `Route::is('welcome')->apply('test.middleware');`
|match|uses regular expression to matche the URI, also support (:string) and (:num) to match string and numeric value| `Route::match('welcome/(:string)/(:num)')->apply(['test.middleware', 'another.middleware']);`
|segment|checks if a specific segment matches a given string| `Route::segment(1, 'welcome')->apply(['test.middleware']);`


##### *✔ Apply via library*_________________

```php
//load library from Twilight
$this->load->library('twilight/middleware/middleware');

//apply
$this->middleware->execMiddleware('guest');
$this->middleware->execMiddleware(['my.middle', 'test.middle']);
```

##### 📑 Note

1. you can apply mittleple middlewares by using array  `->apply(['first', 'second'])`
2. middlewares support parameter, just pass them when after `:` and for multiple param use comma,  like -
```php
Route::match('test/(:string)')->apply(['allow.if:admin', 'for:verified,email']);

$this->middleware->execMiddleware(['member:gold']);
```


#### 🛡️ Hashing

> The Twilight Hash library provides secure Bcrypt and Argon2 hashing for storing user passwords. Bcrypt is a great choice for hashing passwords because its "work factor" is adjustable, which means that the time it takes to generate a hash can be increased as hardware power increases.


#### *✔ Configuration*

the `config/encryption.php` file contains hashing configuration
default algorithm is `bcrypt` Supported: `bcrypt`, `argon`, `argon2id`.
you can additionally configure hash options too.


##### *✔ Usage*

load the library `$this->load->library('twilight/encryption/hash');` then use the below supported methods

| Method | Description | Example |
|-|:-:|-|
|make|generates hashed string of a given value| `$this->hash->make($pwd);`
|match|verify that a given plain-text string corresponds to a given hash| `$this->hash->match($pwd, $hash); //return bool`


#### 🧪 Migration

> Migrations are a convenient way for you to alter your database in a structured and organized manner. You could edit fragments of SQL by hand but you would then be responsible for telling other developers that they need to go and run them. You would also have to keep track of which changes need to be run against the production machines next time you deploy.

Twilight provides a migration library based on the Codeigniter migration class that allow you to `step forward`, `rollback`, `jump` and run all new migrations fluently

###### ✔ USAGE

1. create migration file. check official guide

[https://codeigniter.com/userguide3/libraries/migration.html#id2](https://codeigniter.com/userguide3/libraries/migration.html#id2)
[https://codeigniter.com/userguide3/database/forge.html](https://codeigniter.com/userguide3/database/forge.html)

2. load library `$this->load->library('twilight/migration/migrator');` and use supported methods

###### ✔ SUPPORTED METHODS

| Method | Parameter | Description | Example |
|-|:-:|:-:|-|
|migrateAll|void|run all migrations that are not yet ran| `$this->migrator->migrateAll();`
|jumpTo|jump to a specific migration version in the timeline, can be used to roll back changes or step forwards programmatically to specific versions|string $target_version| `$this->migrator->jumpTo("20200528124400");`
|step|string **$direction** = "forward" or "back"; int **$step_number** |run all migrations that are not yet ran| `try { echo $this->migrator->step("forward", 1); } catch (\Exception $e) {show_error($e->getMessage());}`
