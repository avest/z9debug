<?php
/**
 * This file is part of "Modernizing Legacy Applications in PHP".
 *
 * @copyright 2014 Paul M. Jones <pmjones88@gmail.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Mlaphp;

use debug;
use Facade\Http;
use Facade\Config;
use Z9\Framework\Hooks;
use Facade\Gateway;

use RuntimeException;
use Z9\Cms\Facade\CmsPage;


/**
 * A basic router implementation that converts URL paths to file paths or class
 * names.
 */
class Router
{
	/**
	* The URL path prefix for the front controller.
	*
	* @var string
	*/
	protected $front = '/framework/front.php';

	/**
	* The route value for the home page (URL path `/`).
	*
	* @var string
	*/
	//protected $home_route = '/index.php';
	protected $home_route = 'Z9\Framework\Controller\HomePage';

	/**
	* The route value for when there is no matching route.
	*
	* @var string
	*/
	//protected $not_found_route = '/not-found.php';
	protected $not_found_route = 'Z9\Framework\Controller\NotFound';

	/**
	* The path to the pages directory.
	*
	* @var string
	*/
	protected $pages_dir;

	/**
	* The map of URL paths (keys) to file paths or class names (values).
	*
	* @var array
	*/
	public $routes = array();

	public $user = null;
	public $page = null;
	public $hooks = null;
	public $org_path = null;

	//--------------------------------------------------------
	// enable debugging for all of routing functionality...
	//--------------------------------------------------------
	public $debug = false;

	/**
	* Constructor.
	*
	* @param string $pages_dir The path to the pages directory.
	*/
	public function __construct($pages_dir = null, $user = null, $page = null, $hooks = null)
	{
		debug::on(false);
		debug::variable($pages_dir);
		if ($pages_dir)
		{
			$this->pages_dir = rtrim($pages_dir, '/');
			debug::variable($this->pages_dir);
			$this->user = $user;
			$this->page = $page;
			$this->hooks = $hooks;
		}

	}

	/**
	* Sets the URL path prefix for the front controller.
	*
	* @param string $front The URL path prefix for the front controller.
	* @return null
	*/
	public function setFront($front)
	{
		$this->front = '/' . ltrim($front, '/');
	}

	/**
	* Sets the route value for the home page (URL path `/`).
	*
	* @param string $home_route The route value for the home page.
	* @return null
	*/
	public function setHomeRoute($home_route)
	{
		$this->home_route = $home_route;
	}

	/**
	* Sets the route value for when there is no matching route.
	*
	* @param string $not_found_route The route value for when there is no
	* matching route.
	* @return null
	*/
	public function setNotFoundRoute($not_found_route)
	{
		$this->not_found_route = $not_found_route;
	}

	/**
	* Sets the map of URL paths (keys) to file paths or class names (values).
	*
	* @param array $routes The map of URL paths (keys) to file paths or class
	* names (values).
	* @return null
	*/
	public function setRoutes(array $routes)
	{
		$this->routes = array_merge($this->routes, $routes);
	}

	/**
	* Given a URL path, returns a matching route value (either a file name or
	* a class name).
	*
	* @param string $path The URL path to be routed.
	* @return string A file path or a class name.
	*/

	/*
	 * matches calls
	 * 	this->fix_path()
	 * 	this->getRoute()
	 * 	this->fixRoute()
	 * 		this->isFileRoute()
	 * 		this->fixFileRoute()
	 *
	 */
	public function match($path)
	{
		debug::on(false);
		if ($this->debug) { debug::on(); }
		debug::variable($path);

		$this->org_path = $path;
		debug::variable($this->org_path);

		//------------------------------------------------------------------------------------
		// Redirect if needed, based on redirect settings from $this->page->data['redirect']
		//------------------------------------------------------------------------------------
		if (isset($this->page->data['redirect']) && $this->page->data['redirect'])
		{
			debug::str_exit('Redirecting to: <a href="'.$this->page->data['redirect_path'].'">'.$this->page->data['redirect_path'].'</a>');
			Http::no_cache();
			Http::status($this->page->data['redirect_code']);
			Http::location($this->page->data['redirect_path']);
			exit();
		}

		$fixed_path = $this->fixPath($path);
		debug::variable($fixed_path);

		$route = $this->getRoute($fixed_path);
		debug::variable($route);

		$fixed_route = $this->fixRoute($route);
		debug::variable($fixed_route);

		return $fixed_route;
	}

	/**
	* Fixes the incoming URL path to strip the front controller script
	* name.
	*
	* @param string $path The incoming URL path.
	* @return string The fixed path.
	*/
	protected function fixPath($path)
	{
		debug::on(false);
		if ($this->debug) { debug::on(); }
		debug::variable($path);

		$len = strlen($this->front);
		debug::variable($len);

		if (substr($path, 0, $len) == $this->front)
		{
			$path = substr($path, $len);
			debug::variable($path);
		}

		$fixed_path = '/' . ltrim($path, '/');
		debug::variable($fixed_path);

		return $fixed_path;
	}

	/**
	* Returns the route value for a given URL path; uses the home route value
	* if the URL path is `/`.
	*
	* @param string $path The incoming URL path.
	* @return string The route value.
	*/
	protected function getRoute($path)
	{
		debug::on(false);
		if ($this->debug) { debug::on(); }

		debug::variable($path);

		if (isset($this->user->data))
		{
			debug::variable($this->user->data);
		}
		if (isset($this->page->data))
		{
			debug::variable($this->page->data);
		}

		if (isset($this->routes[$path]))
		{
			debug::string("route = ".$this->routes[$path]);
			return $this->routes[$path];
		}

		// implies these are all valid pages
		if (isset($this->page->data['page_type']) && $this->page->data['page_type'] == Config::get('cms.html_page_type'))
		{
			debug::string("Html Page");
			return 'Z9\Cms\Controller\HtmlPage';
		}
		if (isset($this->page->data['page_type']) && $this->page->data['page_type'] == Config::get('cms.uploaded_doc_page_type'))
		{
			debug::string("Uploaded Doc Page");
			return 'Z9\Cms\Controller\UploadedDocPage';
		}
		if (isset($this->page->data['page_type']) && $this->page->data['page_type'] == Config::get('cms.internal_url_page_type'))
		{
			debug::string("Internal Url Page");
			return 'Z9\Cms\Controller\InternalPage';
		}


		$results = $this->hooks->do_action('router_get_route', $path);
		debug::variable($results);

		if (is_array($results))
		{
			foreach ($results as $result)
			{
				if (!empty($result['result']))
				{
					return $result['result'];
				}
			}
		}


		if ($path == '/')
		{
			debug::string("home route 1");
			return $this->home_route;
		}

		if (isset($this->page->data['page_is_secure']) && $this->page->data['page_is_secure'])
		{
			if (!$this->page->data['user_has_access_to_page'])
			{
				debug::str_exit("redirecting to log-in.php");
				Http::no_cache();
				Http::location("/secure/log-in.php");
				exit();
			}
		}

		//debug::str_exit('controller has been selected');

		return $path;
	}

	/**
	* Fixes a route specification to make sure it is found.
	*
	* @param string $route The matched route.
	* @return string The "fixed" route.
	* @throws RuntimeException when the route is a file but no pages directory
	* is specified.
	*/
	protected function fixRoute($route)
	{
		debug::on(false);
		if ($this->debug) { debug::on(); }

		debug::variable($route);

		$is_file_route = $this->isFileRoute($route);
		debug::variable($is_file_route);

		if ($is_file_route)
		{
			$page_id = '';

			$fixed_file_route = $this->fixFileRoute($route);
			debug::variable($fixed_file_route);

			if ($fixed_file_route <> $this->org_path)
			{
				if ($fixed_file_route <> $this->not_found_route)
				{
					debug::string('Redirecting to '.$fixed_file_route);
					debug::str_exit();
					Http::no_cache();
					Http::location($route);
					exit();
				}
				else
				{
					$route = $fixed_file_route;
					debug::variable($route);
				}
			}
		}

		return $route;
	}

	/**
	* Is the matched route a file name?
	*
	* @param string $route The matched route.
	* @return bool
	*/
	protected function isFileRoute($route)
	{
		debug::on(false);
		if ($this->debug) { debug::on(); }

		debug::variable($route);

		$is_file_route = substr($route, 0, 1) == '/';
		debug::variable($is_file_route);

		return $is_file_route;
	}

	/**
	* Fixes a file route specification by finding the real path to see if it
	* exists in the pages directory and is readable.
	*
	* @param string $route The matched route.
	* @return string The real path if it exists, or the not-found route if it
	* does not.
	* @throws RuntimeException when the route is a file but no pages directory
	* is specified.
	*/
	protected function fixFileRoute($route)
	{
		debug::on(false);
		if ($this->debug) { debug::on(); }

		debug::variable($route);
		debug::variable($this->pages_dir, 'this->pages_dir');
		if (!$this->pages_dir)
		{
			throw new RuntimeException('No pages directory specified.');
		}

		$tmp_path = $this->pages_dir . $route;
		debug::variable($tmp_path);

		// remove url string
		$tmp_path_array = parse_url($tmp_path);
		debug::variable($tmp_path_array);

		if (isset($tmp_path_array['path']))
		{
			$tmp_path = $tmp_path_array['path'];
			debug::variable($tmp_path);
		}

		$page = realpath($tmp_path);
		debug::variable($page);

		$page_exists = $this->pageExists($page);
		debug::variable($page_exists);

		//----------------------
		// physical page exists
		//----------------------
		if ($page_exists)
		{
			debug::string("returning original route because page exists");
			//$fixed_file_route = $page;
			$fixed_file_route = $route;
			debug::variable($fixed_file_route);
			return $fixed_file_route;
		}

		//---------------------------------
		// physical page does not exists
		//---------------------------------

		debug::variable($this->not_found_route);

		// support dependency injection container
		if (substr($this->not_found_route, 0, 17) == 'Z9\Cms\Controller')
		{
			//$return = 'Z9\Cms\Controller\NotFound';
			$return = $this->not_found_route;
			debug::variable($return);
			return $return;
		}

		// the above is always true, so this would never get run...

		// is the not_found_route a file route?
		$not_found_is_file_route = $this->isFileRoute($this->not_found_route);
		debug::variable($not_found_is_file_route);

		if ($not_found_is_file_route)
		{
			debug::string("not_found_route is file route");
			$fixed_file_route = $this->pages_dir . $this->not_found_route;
			debug::variable($fixed_file_route);
			return $fixed_file_route;
		}
		else
		{
			// not_found_route is not a file route, it is a controller route
			debug::string("not_found_route is not file route");
			$fixed_file_route = $this->not_found_route;
			debug::variable($fixed_file_route);
			return $fixed_file_route;
		}
	}

	/**
	* Does the pages directory have a matching readable file?
	*
	* @param string $file The file to check.
	* @return bool
	*/
	protected function pageExists($file)
	{
		debug::on(false);
		if ($this->debug) { debug::on(); }

		debug::variable($file);

		$file_not_empty = $file != '';
		debug::variable($file_not_empty);

		debug::variable($this->pages_dir);
		$confirm_file = str_replace('\\', '/', $file);
		$confirm_pages_dir = str_replace('\\', '/', $this->pages_dir);
		$file_path_correct = substr($confirm_file, 0, strlen($confirm_pages_dir)) == $confirm_pages_dir;
		debug::variable($file_path_correct);

		$file_exists = file_exists($file);
		debug::variable($file_exists);

		$file_readable = is_readable($file);
		debug::variable($file_readable);

		$page_exists = false;
		if ( $file_not_empty &&
			$file_path_correct &&
			$file_exists &&
			$file_readable)
		{
			$page_exists = true;
		}
		debug::variable($page_exists);

		return $page_exists;

	}


	public function load_routes($config_path)
	{
		debug::on(false);
		debug::string('load_routes()');
		debug::variable($config_path);

		$config_dirs = Config::get('site.config_dirs');
		debug::variable($config_dirs);

		if (is_array($config_dirs))
		{
			foreach ($config_dirs as $config_dir)
			{
				$route_files = Config::get_config_files($config_dir, 'routes');
				debug::variable($route_files);

				if (is_array($route_files))
				{
					foreach ($route_files as $route_file)
					{
						debug::variable($route_file);
						include($route_file);
					}
				}
			}
		}
		debug::variable($this->routes);
	}

}

?>