<?php
//===================================================================
// Z9 Framework
//===================================================================
// front.php
// --------------------
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

// Report all PHP errors
error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);

define('APP_ROOT_DIR', substr(dirname(__FILE__), 0, strlen(dirname(__FILE__))-10));

$app_web_dir = substr(APP_ROOT_DIR, strlen($_SERVER['DOCUMENT_ROOT']));
$app_web_dir = str_replace("\\", "/", $app_web_dir);
define('APP_WEB_DIR', $app_web_dir);

define('Z9DEBUG_DIR', APP_ROOT_DIR);

// load debug
require_once(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'load_console.php');

if (!is_file(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'config_settings.php'))
{
	echo "settings/config_settings.php file missing.<br>";
	exit();
}
include(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'config_settings.php');

if (is_file(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'toggle_settings.php'))
{
	include(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'toggle_settings.php');
}
if (is_file(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'file_categories.php'))
{
	include(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'file_categories.php');
}

// include PhpParser class
include(APP_ROOT_DIR.'/vendor/autoload.php');

debug::on(false);
debug::string('front.php');

// add check for HTTPS
if (!debug::get('force_http'))
{
	$is_https = ($_SERVER['SERVER_PORT'] == 443) ? true : false;
	if (!$is_https)
	{
		echo "HTTPS required.<br>";
		exit();
	}
}

// load Autoloader class
require_once(APP_ROOT_DIR.DIRECTORY_SEPARATOR.'framework'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Mlaphp'.DIRECTORY_SEPARATOR.'Autoloader.php');
$autoloader = new \Mlaphp\Autoloader();
spl_autoload_register(array($autoloader, 'load'));
$autoloader->init(APP_ROOT_DIR.'/_config/namespaces.php');

// load Dependency Injection Container class
$di = new \Mlaphp\Di($GLOBALS);

// define Action class
$di->set('Facade\Action', function () use ($di) {
	return new \Facade\Action(
		$di
	);
});
$di->get('Facade\Action');

// define Dependency class
$di->set('Facade\Dependency', function () use ($di) {
	return new \Facade\Dependency(
		$di
	);
});
$di->get('Facade\Dependency');

// define Config class
$di->set('Facade\Config', function () use ($di) {
	return new \Facade\Config();
});

// load facade
$di->set('Facade', function () use ($di) {
	return new \Laravel\Facade(
		$di
	);
});
$di->get('Facade');

// load config settings
use Facade\Config;
Config::init(APP_ROOT_DIR.'/_config/');

// load hooks
$di->set('Z9\Framework\Hooks', function () use ($di) {
	return new \Z9\Framework\Hooks(
		$di
	);
});
$hooks = $di->get('Z9\Framework\Hooks');
$hooks->init();

use Facade\Php;
$php_version = Php::version();
$php_major_version = $php_version['PHP_MAJOR_VERSION'];
debug::variable($php_major_version);


// load request class definition
$di->set('Request', function () use ($di) {
	return new \Mlaphp\Request($GLOBALS);
});

// load framework user info
$di->set('User', function () use ($di) {
	return new \Z9\Framework\User(
	);
});
$user = $di->get('User');
$user->init_data();
debug::variable($user->data);

// load framework page info
$di->set('Page', function () use ($di) {
	return new \Z9\Framework\Page(
	);
});
$page = $di->get('Page');
$page->init_data();
debug::variable($page->data);


$di->set('Response', function () use ($di) {
	return new \Mlaphp\Response(
		APP_ROOT_DIR.'/views',
		$di,
		$di->get('User'),
		$di->get('Page')
	);
});
$response = $di->get('Response');



// set up the router
$di->set('Router', function () use ($di) {
	$router = new \Mlaphp\Router(
		APP_ROOT_DIR.'/',
		$di->get('User'),
		$di->get('Page'),
		$di->get('Z9\Framework\Hooks')
	);
	return $router;
});
$router = $di->get('Router');

// load routes
$router->load_routes(APP_ROOT_DIR.'/_config/routes/');

// match against the url path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
//debug::variable($path);

$route = $router->match($path);
debug::variable($route);

// is there a di container service for the route?
if ($di->has($route))
{
	debug::string('found di container for controller');
	// create a new $controller instance
	$controller = $di->get($route);
	debug::variable($controller);
}
else
{
	debug::string('looking for page script for controller');
	// require the page script, which creates the controller
	require($route);
}

// invoke the controller and send the response
$controller_response = $controller->__invoke();
//debug::variable($response);

if (is_object($controller_response))
{
	//debug::string('is_object');
	$controller_response->send();
}

?>