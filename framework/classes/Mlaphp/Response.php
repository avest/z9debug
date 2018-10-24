<?php
/**
 * This file is part of "Modernizing Legacy Applications in PHP".
 *
 * @copyright 2014 Paul M. Jones <pmjones88@gmail.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Mlaphp;

use debug;
use Facade\Config;

/**
 * Encapsulates a plain old PHP response.
 */
class Response
{
	/**
	* A base path prefix for view files.
	*
	* @var string
	*/
	public $base;

	/**
	* The buffer for HTTP header calls.
	*
	* @var array
	*/
	protected $headers = array();

	/**
	* The callable and arguments to be invoked with `call_user_func_array()`
	* as the last step in the `send()` process.
	*
	* @var array
	*/
	protected $last_call;

	/**
	* Variables to extract into the view scope.
	*
	* @var array
	*/
	public $vars = array();

	/**
	* A view file to require in its own scope.
	*
	* @var string
	*/
	protected $view;

	protected $view_namespace;

	protected $di = null;

	protected $block = array();

	public $jquery_file = '';

	public $js_files = array();

	public $less_files = array();

	public $css_files = array();

	public $user = null;

	public $page = null;

	//public $db = null;

	public $internal_url = array(
		'load_file' => false, // true/false
		'dir_name' => array(),
		'base_name' => array(),
		'physical_file_path' => '',
		'template_vars' => array(),
		'outer_template_file_name' => '',
	);

	// used in front.php to reset server vars
	// for loading internal url page
	public $server_vars = array();

	/**
	* Constructor.
	*
	* @param string $base A base path prefix for view files.
	*/
	public function __construct($base = null, $di = null, $user = null, $page = null)
	{
		$this->setBase($base);
		$this->di = $di;
		//$this->db = $db;
		$this->user = $user;
		$this->page = $page;
	}

	/**
	* Sets the base path prefix for view files.
	*
	* @param string $view The view file.
	* @return null
	*/
	public function setBase($base)
	{
		$this->base = $base;
	}

	/**
	* Gets the base path prefix for view files.
	*
	* @return string
	*/
	public function getBase()
	{
		return $this->base;
	}

	/**
	* Sets the view file.
	*
	* @param string $view The view file.
	* @return null
	*/
	public function setView($view, $namespace='')
	{
		debug::on(false);
		debug::variable($view, 'view');
		debug::variable($namespace, 'namespace');
		$this->view = $view;
		$this->view_namespace = $namespace;
	}

	/**
	* Gets the view file.
	*
	* @return string
	*/
	public function getView()
	{
		return $this->view;
	}

	/**
	* Returns the full path to the view.
	*
	* @return string
	*/
	public function getViewPath()
	{
		debug::on(false);
		debug::string('getViewPath()');

		$base_path = $this->base;
		debug::variable($this->base, 'this->base');
		debug::variable($this->view, 'this->view');
		debug::variable($this->view_namespace, 'this->view_namespace');

		if (!empty($this->view_namespace))
		{
			$namespace_prefixes = Config::get('site.namespace_prefixes');
			if (isset($namespace_prefixes[$this->view_namespace]['views_dir']))
			{
				$base_path = $namespace_prefixes[$this->view_namespace]['views_dir'];
			}
		}
		debug::variable($base_path, 'base_path');

		if (! $base_path)
		{
			return $this->view;
		}

		$return = rtrim($base_path, DIRECTORY_SEPARATOR)
			. DIRECTORY_SEPARATOR
			. ltrim($this->view, DIRECTORY_SEPARATOR);
		debug::variable($return, 'return');
		return $return;
	}

	/**
	* Sets the variables to be extracted into the view scope.
	*
	* @param array $vars The variables to be extracted into the view scope.
	* @return null
	*/
	public function setVars(array $vars)
	{
		unset($vars['this']);
		$this->vars = $vars;
	}

	public function mergeVars(array $vars)
	{
		unset($vars['this']);
		if (is_array($vars))
		{
			$this->vars = array_merge($this->vars, $vars);
		}
	}


	/**
	* Gets the variables to be extracted into the view scope.
	*
	* @return array
	*/
	public function getVars()
	{
		return $this->vars;
	}

	/**
	* Sets the callable to be invoked with `call_user_func_array()` as the
	* last step in the `send()` process; extra arguments are passed to the
	* call.
	*
	* @param callable $func The callable to be invoked.
	* @return null
	*/
	public function setLastCall($func)
	{
		$this->last_call = func_get_args();
	}

	/**
	* Gets the callable to be invoked with `call_user_func_array()` as the
	* last step in the `send()` process.
	*
	* @return callable
	*/
	public function getLastCall()
	{
		return $this->last_call;
	}

	/**
	* Escapes output for HTML tag contents, or for a **quoted** HTML
	* attribute. Unquoted attributes are not made safe by using this method,
	* nor is non-HTML content.
	*
	* @param string $string The unescaped string.
	* @return string
	*/
	public function esc($string)
	{
		return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
	}

	/**
	* Buffers a call to `header()`.
	*
	* @return null
	*/
	public function header()
	{
		$args = func_get_args();
		array_unshift($args, 'header');
		$this->headers[] = $args;
	}

	/**
	* Buffers a call to `setcookie()`.
	*
	* @return bool
	*/
	public function setCookie()
	{
		$args = func_get_args();
		array_unshift($args, 'setcookie');
		$this->headers[] = $args;
		return true;
	}

	/**
	* Buffers a call to `setrawcookie()`.
	*
	* @return bool
	*/
	public function setRawCookie()
	{
		$args = func_get_args();
		array_unshift($args, 'setrawcookie');
		$this->headers[] = $args;
		return true;
	}

	/**
	* Returns the buffer for HTTP header calls.
	*
	* @return bool
	*/
	public function getHeaders()
	{
		return $this->headers;
	}

	/**
	* Outputs the buffered headers, buffered view, and calls the user function.
	*
	* @return null
	*/
	public function send()
	{
		debug::on(false);
		debug::string('send()');
		//debug::variable($this, 'this');
		$buffered_output = $this->requireView();
		debug::variable($buffered_output, 'buffered_output');
		$this->sendHeaders();
		echo $buffered_output;
		$this->invokeLastCall();
	}

	/**
	* Requires the view in its own scope with extracted variables and returns
	* the buffered output.
	*
	* @return string
	*/
	public function requireView()
	{
		debug::on(false);
		debug::variable($this->view, 'this->view');

		$is_view_empty = false;
		if (! $this->view)
		{
			$is_view_empty = true;
		}
		debug::variable($is_view_empty);

		$view_path = '';
		$is_view_missing = true;
		if (!$is_view_empty)
		{
			extract($this->vars);
			ob_start();
			$view_path = $this->getViewPath();
			debug::variable($view_path, 'view_path');
			if (is_file($view_path))
			{
				$is_view_missing = false;
				require($view_path);
			}
		}
		debug::variable($is_view_missing);

		if ($is_view_empty || $is_view_missing)
		{
			echo "<style>";
			echo "BODY {";
			echo "padding:20px;";
			echo "font-size:14px;";
			echo "font-family: Arial;";
			echo "}";
			echo "</style>";
			echo "<h3>ERROR</h3>";
			echo "Template not found / not set<br><br>";
			echo "File: ".$view_path."<br><br>";
			if (Config::get('site.is_dev'))
			{
				debug::enabled(true);
				debug::suppress_output(false);
				debug::on();
				debug::string('ERROR: Template not found / not set');
				debug::variable($view_path);
				debug::stack_trace();
			}
			if (Config::get('cms.is_installed'))
			{
				//debug::string('cms is installed');
				if (!empty($this->page->data['page_id']))
				{
					//debug::string('is cms page');
					if ($this->user->data['be_is_logged_in'])
					{
						//debug::string('be_is_logged_in');
						echo '<a href="/cms/admin/edit_page.php?pageid='.$this->page->data['page_id'].'&cut=&cpy=&page_nbr=1">Edit Page</a><br><br>';
					}
				}
			}
			exit();
		}
		$result = ob_get_clean();
		debug::variable($result, 'result');
		return $result;
	}

	/**
	* Outputs the buffered calls to `header`, `setcookie`, etc.
	*
	* @return null
	*/
	public function sendHeaders()
	{
		debug::on(false);
		foreach ($this->headers as $args)
		{
			debug::variable($args, 'args');
			$func = array_shift($args);
			call_user_func_array($func, $args);
		}
	}

	/**
	* Invokes `$this->call`.
	*
	* @return null
	*/
	public function invokeLastCall()
	{
		if (! $this->last_call)
		{
			return;
		}
		$args = $this->last_call;
		$func = array_shift($args);
		call_user_func_array($func, $args);
	}

	// Added by Z9digital
	public function __get($name)
	{
		debug::on(false);
		debug::variable($name, 'name');
		return $this->vars[$name];
	}

	// Added by Z9digital
	public function __set($name, $value)
	{
		if ( $name == 'base' ||
			$name == 'headers' ||
			$name == 'last_call' ||
			$name == 'vars' ||
			$name == 'view'
		)
		{
			throw new Exception("Cannot bind variable named ".$name);
		}
		$this->vars[$name] = $value;
	}

	// Added by Z9digital
	public function render($view='', $namespace='')
	{
		debug::on(false);
		debug::variable($view, 'view');
		debug::variable($namespace, 'namespace');
		//debug::variable($this, 'this');


		$base_path = $this->base;
		if (!empty($namespace))
		{
			$namespace_prefixes = Config::get('site.namespace_prefixes');
			debug::variable($namespace_prefixes, 'namespace_prefixes');

			if (isset($namespace_prefixes[$namespace]['views_dir']))
			{
				$base_path = $namespace_prefixes[$namespace]['views_dir'];
			}
		}
		debug::variable($base_path, 'base_path');


		if (empty($view))
		{
			return '';
		}
		$this->view = $view;

		extract($this->vars);
		ob_start();

		$view_path = rtrim($base_path, DIRECTORY_SEPARATOR)
			. DIRECTORY_SEPARATOR
			. ltrim($view, DIRECTORY_SEPARATOR);
		debug::variable($view_path, 'view_path');
		//debug::str_exit('test');
		if (is_file($view_path))
		{
			require ($view_path);
		}
		else
		{
			debug::enabled(true);
			debug::on();
			debug::string('ERROR: view path not found.');
			debug::variable($view_path, 'view_path');
			debug::stack_trace();
			exit();
		}
		return ob_get_clean();
	}

	// Added by Z9digital
	public function renderBlock($block_name, $data=array())
	{
		debug::on(false);
		debug::string('renderBlock');
		debug::variable($block_name, 'block_name');
		debug::variable($this->di, 'this->di');

		$namespace_prefixes = Config::get('site.namespace_prefixes');
		debug::variable($namespace_prefixes, 'namespace_prefixes');

		$prefix = '';
		if (is_array($namespace_prefixes))
		{
			foreach ($namespace_prefixes as $namespace_prefix => $namespace_prefix_path)
			{
				if (strpos($block_name, $namespace_prefix) === 0)
				{
					debug::string('found match');
					$prefix = $namespace_prefix;

					// update the class to not have the prefix
					$block_name = substr($block_name, strlen($namespace_prefix) + 1);
					debug::variable($block_name, 'block_name');
				}
			}
		}
		debug::variable($prefix, 'prefix');
		debug::variable($block_name, 'block_name');

		//$block_name_class = 'Block\\'.$block_name.'_block';
		$block_name_class = $prefix.'\\Block\\'.$block_name.'_block';
		debug::variable($block_name_class, 'block_name_class');

		$this->block[$block_name] = $this->di->get($block_name_class);
		debug::variable($this->block, 'this->block');


		$this->block[$block_name]->base = $this->base;
		$this->block[$block_name]->user = $this->user;
		$this->block[$block_name]->page = $this->page;
		//$this->block[$block_name]->db = $this->db;
		$this->block[$block_name]->di = $this->di;

		if (!empty($data))
		{
			return $this->block[$block_name]->display($data);
		}
		else
		{
			return $this->block[$block_name]->display($data);
		}

	}

	// Added by Z9digital
	public function jquery_include_once($jquery_src_file)
	{
		debug::on(false);
		debug::variable($jquery_src_file, 'jquery_src_file');

		$content = '';
		if (empty($this->jquery_file))
		{
			$this->jquery_file = $jquery_src_file;
			$content = '<script type="text/javascript" src="'.$jquery_src_file.'"></script>'."\n";
		}
		return $content;
	}

	// Added by Z9digital
	public function less_include_once($less_src_file)
	{
		debug::on(false);
		debug::variable($less_src_file, 'less_src_file');

		$content = '';
		if (!in_array($less_src_file, $this->less_files))
		{
			$this->less_files[] = $less_src_file;
			$content = '<link rel="stylesheet/less" type="text/css" href="'.$less_src_file.'" />'."\n";
		}
		return $content;
	}

	// Added by Z9digital
	public function js_include_once($js_src_file)
	{
		debug::on(false);
		debug::variable($js_src_file, 'js_src_file');

		$content = '';
		if (!in_array($js_src_file, $this->js_files))
		{
			$this->js_files[] = $js_src_file;
			$content = '<script type="text/javascript" src="'.$js_src_file.'"></script>'."\n";
		}
		return $content;
	}

	// Added by Z9digital
	public function js_include($js_src_file)
	{
		debug::on(false);
		debug::variable($js_src_file, 'js_src_file');

		if (!in_array($js_src_file, $this->js_files))
		{
			$this->js_files[] = $js_src_file;
		}
		$content = '<script type="text/javascript" src="'.$js_src_file.'"></script>'."\n";
		return $content;
	}

	// Added by Z9digital
	public function css_include_once($css_src_file, $media_attribute = '')
	{
		debug::on(false);
		debug::variable($css_src_file, 'css_src_file');

		$content = '';
		if (!in_array($css_src_file, $this->css_files))
		{
			$this->css_files[] = $css_src_file;
			$display_media = (!empty($media_attribute)) ? ' media="'.$media_attribute.'"' : '';
			$content = '<link href="'.$css_src_file.'" rel="stylesheet" type="text/css"'.$display_media.' />'."\n";
		}
		return $content;
	}

	// Added by Z9digital
	public function css_include($css_src_file, $media_attribute = '')
	{
		debug::on(false);
		debug::variable($css_src_file, 'css_src_file');

		if (!in_array($css_src_file, $this->css_files))
		{
			$this->css_files[] = $css_src_file;
		}
		$display_media = (!empty($media_attribute)) ? ' media="'.$media_attribute.'"' : '';
		$content = '<link href="'.$css_src_file.'" rel="stylesheet" type="text/css"'.$display_media.' />'."\n";
		return $content;
	}

	//----------------------------------------------------
	// SAVE AND RESET SERVER VARIABLES TO HIDE FRONT.PHP
	//----------------------------------------------------
	function reset_server_vars($dir_name, $base_name)
	{
		global $PHP_SELF, $_SERVER;

		debug::on(false);
		debug::string('reset_server_vars()');
		debug::variable($dir_name, 'dir_name');
		debug::variable($base_name, 'base_name');

		// Save current environment settings
		$this->server_vars['php_self'] = $PHP_SELF;
		$this->server_vars['script_filename'] = $_SERVER['SCRIPT_FILENAME'];
		$this->server_vars['script_name'] = $_SERVER['SCRIPT_NAME'];
		$this->server_vars['path_translated'] = $_SERVER['PATH_TRANSLATED'];
		$this->server_vars['cwd'] = getcwd();
		debug::variable($this->server_vars, 'this->server_vars');

		// Reset environment settings
		$PHP_SELF = substr($dir_name.$base_name, strlen(APP_ROOT_DIR));
		debug::variable($PHP_SELF, 'PHP_SELF');

		$_SERVER['PHP_SELF'] = substr($dir_name.$base_name, strlen(APP_ROOT_DIR));
		$_SERVER['SCRIPT_FILENAME'] = $dir_name.$base_name;
		$_SERVER['SCRIPT_NAME'] = substr($dir_name.$base_name, strlen(APP_ROOT_DIR));
		$_SERVER['PATH_TRANSLATED'] = $dir_name.$base_name;
		debug::variable($_SERVER['PHP_SELF'], '_SERVER[PHP_SELF]');
		debug::variable($_SERVER['SCRIPT_FILENAME'], '_SERVER[SCRIPT_FILENAME]');
		debug::variable($_SERVER['SCRIPT_NAME'], '_SERVER[SCRIPT_NAME]');
		debug::variable($_SERVER['PHP_TRANSLATED'], '_SERVER[PATH_TRANSLATED]');

		if (is_dir($dir_name))
		{
			chdir($dir_name);
		}
		$current_working_directory = getcwd();
		debug::variable($current_working_directory, 'current_working_directory');

		return true;
	}

	//-------------------------------------------
	// RESTORE SERVER VARIABLES
	//-------------------------------------------
	function restore_server_vars()
	{
		debug::on(false);
		debug::string('restore_server_vars()');

		global $PHP_SELF, $_SERVER;

		// Restore environment variables
		$PHP_SELF = $this->server_vars['php_self'];
		debug::variable($PHP_SELF, 'PHP_SELF');

		$_SERVER['PHP_SELF'] = $this->server_vars['php_self'];
		$_SERVER['SCRIPT_FILENAME'] = $this->server_vars['script_filename'];
		$_SERVER['SCRIPT_NAME'] = $this->server_vars['script_name'];
		$_SERVER['PATH_TRANSLATED'] = $this->server_vars['path_translated'];
		debug::variable($_SERVER['PHP_SELF'], '_SERVER[PHP_SELF]');
		debug::variable($_SERVER['SCRIPT_FILENAME'], '_SERVER[SCRIPT_FILENAME]');
		debug::variable($_SERVER['SCRIPT_NAME'], '_SERVER[SCRIPT_NAME]');
		debug::variable($_SERVER['PHP_TRANSLATED'], '_SERVER[PATH_TRANSLATED]');

		if (isset($tmp_cwd))
		{
			chdir ($tmp_cwd);
		}
		$current_working_directory = getcwd();
		debug::variable($current_working_directory, 'current_working_directory');

		return true;
	}

	/*
	 * @$statusCode:
	 * 		404 - Page Not found
	 * 		301 - Permanent Redirect
	 */
	function redirect($url, $statusCode = 301)
	{
		if (headers_sent() === false)
		{
			header('Location: ' . $url, true, $statusCode);
		}
		else
		{
			echo '<meta http-equiv="Location" content="' . $url . '">';
		}
		exit();
	}


	//--------------------------------------------------------
	// check authentication on staging and development sites
	//--------------------------------------------------------
	// exclude search crawler
	// exclude job scheduler
	// exclude tracking session job
	public function enforce_staging_authentication()
	{
		debug::on(false);
		//debug::suppress_output(true);
		debug::string('enforce_staging_authentication()');

		debug::variable(Config::get('site.is_dev'), 'site.is_dev');
		debug::variable(Config::get('site.is_stag'), 'site.is_stag');
		debug::variable(Config::get('site.password_protect_prod'), 'site.password_protect_prod');

		$ok_to_proceed = false;
		if (Config::get('site.is_stag') || Config::get('site.is_dev') || Config::get('site.password_protect_prod'))
		{
			debug::string('is dev, stage, or protect prod');
			if (Config::get('framework.password_protect_staging'))
			{
				debug::string('password protection is on');

				$bypass_url = false;
				if (is_array(Config::get('framework.bypass_staging_auth_urls')) &&
					in_array($this->page->data['full_url'], Config::get('framework.bypass_staging_auth_urls')))
				{
					$bypass_url = true;
				}
				if (is_array(Config::get('framework.bypass_staging_auth_urls')) &&
					in_array($this->page->data['url_path'], Config::get('framework.bypass_staging_auth_urls')))
				{
					$bypass_url = true;
				}
				debug::variable($bypass_url);


				if (!$bypass_url)
				{
					debug::string('not bypassing url');
					if (isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] <> 'onsite_search')
					{
						$ok_to_proceed = true;
					}
				}
				else
				{
					debug::string('bypassing url');
				}
			}
		}
		debug::variable($ok_to_proceed, 'ok_to_proceed');

		if ($ok_to_proceed)
		{
			$cms_cookie_name = 'site_auth';
			$cms_cookie_value = '';
			$cms_user = '';
			$cms_cookie_issued = '';
			$cms_cookie_expired = '';
			$cms_cookie_hash = '';
			$cms_calc_hash = '';
			$cms_public_part = '';
			if (isset($_COOKIE[$cms_cookie_name]))
			{
				$cms_cookie_value = $_COOKIE[$cms_cookie_name];
				if (!empty($cms_cookie_value))
				{
					list($cms_user, $cms_cookie_issued, $cms_cookie_expired, $cms_cookie_hash) = explode(":", $cms_cookie_value, 4);
					$cms_public_part = $cms_user.":".$cms_cookie_issued.":".$cms_cookie_expired;
					$cms_calc_hash = md5(Config::get('framework.site_protect_secret').":".md5($cms_public_part.":".Config::get('framework.site_protect_secret')));
				}
			}
			debug::variable($cms_cookie_name, 'cms_cookie_name');
			debug::variable($cms_cookie_value, 'cms_cookie_value');
			debug::variable($cms_user, 'cms_user');
			debug::variable($cms_cookie_issued, 'cms_cookie_issued');
			debug::variable($cms_cookie_expired, 'cms_cookie_expired');
			debug::variable($cms_cookie_hash, 'cms_cookie_hash');
			debug::variable($cms_public_part, 'cms_public_part');
			debug::variable($cms_calc_hash, 'cms_calc_hash');

			$is_valid_login = false;
			if ($cms_calc_hash == $cms_cookie_hash and strlen($cms_cookie_hash) > 0)
			{
				$is_valid_login = true;
			}
			debug::variable($is_valid_login);

			if (!$is_valid_login)
			{
				$redir = urlencode($this->page->data['full_url']);
				debug::variable($redir, 'redir');

				header('Location: /secure/site_protect?redir='.$redir);
				exit;
			}

		}
	}

}

?>