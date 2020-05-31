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

#### 🔧 Middleware

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






















```bash
composer require devsrv/laravel-session-out
```

> Laravel 5.5+ users: this step may be skipped, as we can auto-register the package with the framework.

```php

// Add the ServiceProvider to the providers array in
// config/app.php

'providers' => [
    '...',
    'devsrv\sessionout\sessionExpiredServiceProvider::class',
];
```

You need to publish the `blade`, `js`, `css` and `config` files included in the package using the following artisan command:
```bash
php artisan vendor:publish --provider="devsrv\sessionout\sessionExpiredServiceProvider"
```


## ⚗️ Usage

just include the blade file to all the blade views which are only available to authenticated users.

```php
@include('vendor.sessionout.notify')
```

> rather copying this line over & over to the views, extend your base blade view and include it there in the bottom



## 🛠  Configuration

#### ✔ The Config File

publishing the vendor will create `config/expiredsession.php` file

```php
return [
	// the number of seconds between ajax hits to check auth session
    'gap_seconds' => 30,
    
    // whether using broadcasting feature to make the modal disappear faster
    'avail_broadcasting' => false,
```

#### ✔ If you want to take advantage of broadcasting

> ** if you are using `avail_broadcasting = true` i.e. want to use the Laravel Echo for faster output please follow the below steps

1. setup [broadcasting](https://laravel.com/docs/master/broadcasting) for your app
and start `usersession` queue worker
```bash
php artisan queue:work --queue=default,usersession
```

2. make sure to put the broadcasting client config `js` file above the `@include` line not below it, in your blade view.
```php
<script type="text/javascript" src="{{ asset('js/broadcasting.js') }}"></script>
//some html between
@include('vendor.sessionout.notify')
```
3. in `App\Providers\BroadcastServiceProvider` file in the `boot` method require the package's channel file, it contains private channel authentication
```php
require base_path('vendor/devsrv/laravel-session-out/src/routes/channels.php');
```
4. in all the places from where users are authenticated call `devsrv\sessionout\classes\AuthState::sessionAvailable()` .
if you are using custom logic to login users then put the line inside your authentication method when login is successful. 
> if you are using laravel's default authentication system then better choice will be to create a listener of the login event, Example :-
```php
// App\Providers\EventServiceProvider

protected $listen = [
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\SuccessfulLogin',
        ],
    ];
```
```php
// App\Listeners\SuccessfulLogin

use devsrv\sessionout\classes\AuthState;

/**
* Handle the event.
*
* @param  Login  $event
* @return void
*/
public function handle(Login $user)
{
	AuthState::sessionAvailable();
}
```


#### ✔ Update the modal design & contents

The modal is created with pure `js` and `css` no framework has been used, so you can easily customize the modal contents by editing the `views/vendor/sessionout/modal.blade.php` & the design by editing `public/vendor/sessionout/css/session-modal.css`

#### ✔ Advanced

- 🔘 if you want to customize the `js` file which is responsible for checking auth session & modal display then modify the `public/vendor/sessionout/js/main.js` file but don't forget to compile it with webpack & place the compiled `js` as `public/vendor/sessionout/dist/js/main.js`

- 🔘 **you may want to create a login form** in the modal, first create the html form in the `views/vendor/sessionout/modal.blade.php` then put the ajax code in `public/vendor/sessionout/js/main.js` & don't forget to compile as mentioned above,
> after ajax success close the modal by calling the `closeSessionOutModal()` function


## 🧐📑 Note

#### ♻ When updating the package

Remember to publish the `assets`, `views` and `config` after each update

use `--force` tag after updating the package to publish the **updated latest** package `assets`, `views` and `config` 
> but remember using _--force_ tag will replace all the publishable files

```bash
php artisan vendor:publish --provider="devsrv\sessionout\sessionExpiredServiceProvider" --force

php artisan vendor:publish --provider="devsrv\sessionout\sessionExpiredServiceProvider" --tag=public --force
```

> when updating the package take backup of the `config/expiredsession.php` file & `public/vendor/sessionout`, `views/vendor/sessionout` directories as the files inside these dir. are configurable so if you modify the files then the updated published files will not contain the changes, though after publishing the `assets`, `views` and `config` you may again modify the files

#### 🔧 After you tweak things

Run this artisan command after changing the config file.
```bash
php artisan config:clear
php artisan queue:restart // only when using broadcasting
```

## 👋🏼 Say Hi! 
Let me know in [Twitter](https://twitter.com/srvrksh) | [Facebook](https://www.facebook.com/srvrksh) if you find this package useful 👍🏼


## 🎀 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
