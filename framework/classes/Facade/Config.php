<?php
//===================================================================
// Z9 Framework
//===================================================================
// Config.php
// --------------------
//       Date Created: 2003-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

// function set_site_settings()
// function determine_site()


namespace Facade;

use debug;
use Facade\Arrays;

class Config
{
	protected static $values = array();
	protected static $config_path = '';
	//protected static $loaded_files = array();
	public static $namespace_prefixes = array();
	public static $function_files = array();

	public static function get_function_files()
	{
		//return $this->function_files;
		return self::$function_files;
	}

	public static function add_functions_dir($functions_dir)
	{
		debug::on(false);
		//debug::string('add_functions_dir()');
		//debug::variable($functions_dir, 'functions_dir');

		$file_list = array();
		if (is_dir($functions_dir))
		{
			$file_list = array();
			if ($handle = @opendir($functions_dir))
			{
				while (false !== ($file = readdir($handle)))
				{
					//debug::variable($file, 'file');
					//if ($file <> '.' and $file <> '..' and !@is_dir($dir_path.'/'.$file) )
					if ($file <> '.' and $file <> '..')
					{
						$file_list[] = $functions_dir.DIRECTORY_SEPARATOR.$file;
					}
				}
				closedir($handle);
			}
		}
		//debug::variable($file_list, 'file_list');

		self::$function_files = array_merge(self::$function_files, $file_list);
	}

	public static function get($key='')
	{
		debug::on(false);
		@list($pkg, $var1, $var2, $var3, $var4) = @explode('.', $key);
		//debug::variable($pkg, 'pkg');
		//debug::variable($var1, 'var1');
		//debug::variable($var2, 'var2');
		//debug::variable($var3, 'var3');
		//debug::variable($var4, 'var4');

//		if (!empty($var1))
//		{
//			if (!isset($this->loaded_files[$pkg]) && !isset($this->loaded_files[$pkg][$var1]))
//			{
//				if (!$this->load_file($pkg))
//				{
//					$this->load_file($pkg, $var1);
//				}
//			}
//		}
//		else
//		{
//			if (!isset($this->loaded_files[$pkg]))
//			{
//				$this->load_file($pkg);
//			}
//		}


		if (empty($key))
		{
			return self::$values;
		}
		else
		{
			if (!empty($var4))
			{
				if (isset(self::$values[$pkg][$var1][$var2][$var3][$var4]))
				{
					return self::$values[$pkg][$var1][$var2][$var3][$var4];
				}
				else
				{
					return '';
				}
			}
			if (!empty($var3))
			{
				if (isset(self::$values[$pkg][$var1][$var2][$var3]))
				{
					return self::$values[$pkg][$var1][$var2][$var3];
				}
				else
				{
					return '';
				}
			}
			elseif (!empty($var2))
			{
				if (isset(self::$values[$pkg][$var1][$var2]))
				{
					return self::$values[$pkg][$var1][$var2];
				}
				else
				{
					return '';
				}
			}
			elseif (!empty($var1))
			{
				if (isset(self::$values[$pkg][$var1]))
				{
					return self::$values[$pkg][$var1];
				}
				else
				{
					return '';
				}
			}
			else
			{
				if (isset(self::$values[$key]))
				{
					return self::$values[$key];
				}
				else
				{
					return '';
				}
			}
		}
	}

//	public static function load_file($pkg, $var1='')
//	{
//		debug::on(false);
//		debug::variable($pkg, 'pkg');
//		debug::variable($var1, 'var1');
//		if (!empty($var1))
//		{
//			if (isset($this->loaded_files[$pkg][$var1]))
//			{
//				return true;
//			}
//			else
//			{
//				if (is_file($this->config_path.$pkg.'.'.$var1.'.php'))
//				{
//					$this->loaded_files[$pkg][$var1] = true;
//					include($this->config_path.$pkg.'.'.$var1.'.php');
//					return true;
//				}
//				else
//				{
//					return false;
//				}
//			}
//		}
//		else
//		{
//			if (isset($this->loaded_files[$pkg]))
//			{
//				return true;
//			}
//			else
//			{
//				if (is_file($this->config_path.$pkg.'.php'))
//				{
//					$this->loaded_files[$pkg] = true;
//					include($this->config_path.$pkg.'.php');
//					return true;
//				}
//				else
//				{
//					return false;
//				}
//			}
//		}
//	}

	public static function merge_array($key, $value)
	{
		debug::on(false);
		debug::variable($key);
		debug::variable($value);

		if (is_array($value))
		{
			$curr_value = self::get($key);
			if (empty($curr_value))
			{
				$curr_value = array();
			}
			debug::variable($curr_value);

			$new_value = array_merge($curr_value, $value);
			debug::variable($new_value);

			self::set($key, $new_value);
		}
	}

	public static function merge($key, $value)
	{
		debug::on(false);
		debug::default_limit(1000);
		debug::variable($key, 'key');
		debug::variable($value, 'value');
		@list($pkg, $var, $var2, $var3, $var4) = @explode('.', $key);
		debug::variable($pkg, 'pkg');
		debug::variable($var, 'var');
		debug::variable($var2, 'var2');
		debug::variable($var3, 'var3');
		debug::variable($var4, 'var4');

		$org_value = $value;

		if (!is_array($value))
		{
			$value = array($value);
		}

		if (!empty($var4))
		{
			//debug::string('!empty(var4)');
			if (isset(self::$values[$pkg][$var][$var2][$var3][$var4]))
			{
				//debug::string('isset()');
				if (!is_array(self::$values[$pkg][$var][$var2][$var3][$var4]))
				{
					//debug::string('!is_array()');
					self::$values[$pkg][$var][$var2][$var3][$var4] = array(self::$values[$pkg][$var][$var2][$var3][$var4]);
				}
				self::$values[$pkg][$var][$var2][$var3][$var4] = array_merge(self::$values[$pkg][$var][$var2][$var3][$var4], $value);
			}
			else
			{
				//debug::string('!isset()');
				self::$values[$pkg][$var][$var2][$var3][$var4] = $org_value;
			}
			debug::variable(self::$values[$pkg][$var][$var2][$var3][$var4], 'self::$values['.$pkg.']['.$var.']['.$var2.']['.$var3.']['.$var4.']');
		}
		elseif (!empty($var3))
		{
			if (isset(self::$values[$pkg][$var][$var2][$var3]))
			{
				if (!is_array(self::$values[$pkg][$var][$var2][$var3]))
				{
					self::$values[$pkg][$var][$var2][$var3] = array(self::$values[$pkg][$var][$var2][$var3]);
				}
				self::$values[$pkg][$var][$var2][$var3] = array_merge(self::$values[$pkg][$var][$var2][$var3], $value);
			}
			else
			{
				self::$values[$pkg][$var][$var2][$var3] = $org_value;
			}
			//debug::variable(self::$values[$pkg][$var][$var2][$var3], 'self::$values['.$pkg.']['.$var.']['.$var2.']['.$var3.']');
		}
		elseif (!empty($var2))
		{
			if (isset(self::$values[$pkg][$var][$var2]))
			{
				if (!is_array(self::$values[$pkg][$var][$var2]))
				{
					self::$values[$pkg][$var][$var2] = array(self::$values[$pkg][$var][$var2]);
				}
				self::$values[$pkg][$var][$var2] = array_merge(self::$values[$pkg][$var][$var2], $value);
			}
			else
			{
				self::$values[$pkg][$var][$var2] = $org_value;
			}
			//debug::variable(self::$values[$pkg][$var][$var2], 'self::$values['.$pkg.']['.$var.']['.$var2.']');
		}
		elseif (!empty($var))
		{
			if (isset(self::$values[$pkg][$var]))
			{
				if (!is_array(self::$values[$pkg][$var]))
				{
					self::$values[$pkg][$var] = array(self::$values[$pkg][$var]);
				}
				self::$values[$pkg][$var] = array_merge(self::$values[$pkg][$var], $value);
			}
			else
			{
				self::$values[$pkg][$var] = $org_value;
			}
			//debug::variable(self::$values[$pkg][$var], 'self::$values['.$pkg.']['.$var.']');
		}
		else
		{
			if (is_array($org_value))
			{
				debug::string('org_value is array');
				debug::variable($org_value, 'org_value');
				debug::variable($value, 'value');
				foreach ($org_value as $key2 => $value2)
				{
					debug::variable($key, 'key');
					debug::variable($key2, 'key2');
					debug::variable($value2, 'value2');

					if (isset(self::$values[$key][$key2]))
					{
						debug::string('self::$values['.$key.']['.$key2.'] is set.');
						if (!is_array($value2))
						{
							debug::string('value2 is not array');
							$value2 = array($value2);
							debug::variable($value2, 'value2');
						}
						debug::variable(self::$values[$key][$key2], 'self::$values['.$key.']['.$key2.']');
						self::$values[$key] = array_merge(self::$values[$key], $value2);
						debug::variable(self::$values[$key], 'self::$values['.$key.']');
					}
					else
					{
						self::$values[$key][$key2] = $value2;
					}
					debug::variable(self::$values[$key][$key2], 'self::$values['.$key.']['.$key2.']');
				}
			}
			else
			{
				if (isset(self::$values[$key]))
				{
					if (!is_array(self::$values[$key]))
					{
						self::$values[$key] = array(self::$values[$key]);
					}
					if (!is_array($org_value))
					{
						$org_value = array($org_value);
					}
					self::$values[$key] = array_merge(self::$values[$key], $org_value);
				}
				else
				{
					self::$values[$key] = $org_value;
				}
			}
			debug::variable(self::$values[$key], 'self::$values['.$key.']');

		}
	}

	public static function set($key, $value)
	{
		@list($pkg, $var, $var2, $var3, $var4) = @explode('.', $key);

		if (!empty($var4))
		{
			self::$values[$pkg][$var][$var2][$var3][$var4] = $value;
		}
		elseif (!empty($var3))
		{
			self::$values[$pkg][$var][$var2][$var3] = $value;
		}
		elseif (!empty($var2))
		{
			self::$values[$pkg][$var][$var2] = $value;
		}
		elseif (!empty($var))
		{
			self::$values[$pkg][$var] = $value;
		}
		else
		{
			if (is_array($value))
			{
				foreach ($value as $key2 => $value2)
				{
					self::$values[$key][$key2] = $value2;
				}
			}
		}
	}

	public static function get_config_files($config_dir, $folder)
	{
		debug::on(false);
		//debug::string('get_config_files()');
		//debug::variable($config_dir, 'config_dir');
		//debug::variable($folder, 'folder');

		$dir_path = $config_dir.$folder;
		//debug::variable($dir_path, 'dir_path');

		$known_pages_file_found = false;

		$dir_file_list = array();
		if (is_dir($dir_path))
		{
			$dir_file_list = @scandir($dir_path);
			//debug::variable($dir_file_list, 'dir_file_list');
		}

		$file_list = array();
		if (is_array($dir_file_list))
		{
			foreach ($dir_file_list as $file)
			{
				//debug::variable($file, 'file');
				//if ($file <> '.' and $file <> '..' and !@is_dir($dir_path.'/'.$file) )
				if ($file <> '.' and $file <> '..')
				{
					// file must end with $folder.php, eg: cms_settings.php
					// make an exception for known_pages.php
					$ends_with = $folder.'.php';
					//debug::variable($ends_with, 'ends_with');

					if (strpos($file, $ends_with) !== false || $file == 'known_pages.php')
					{
						$file_list[] = $dir_path.DIRECTORY_SEPARATOR.$file;
						if ($file == 'known_pages.php')
						{
							$known_pages_file_found = true;
							//debug::variable($known_pages_file_found, 'known_pages_file_found');
						}
					}
				}
			}
		}
		//debug::variable($file_list, 'file_list');
		//debug::variable($known_pages_file_found, 'known_pages_file_found');

		$settings_config_dir = APP_ROOT_DIR.DIRECTORY_SEPARATOR.'_config'.DIRECTORY_SEPARATOR;
		//debug::variable($settings_config_dir, 'settings_config_dir');


		if (! $known_pages_file_found &&
			Config::get('cms.is_installed') &&
			$folder == 'settings' &&
			$config_dir == $settings_config_dir)
		{
			debug::enabled(true);
			debug::on();
			debug::str_exit('ERROR: known_pages.php not found.');
		}
		//debug::str_exit();
		return $file_list;
	}

	// set:
	// site.domain_name
	// site.namespace_prefixes
	// site.ip_address
	// site.root_dir
	// site.has_ssl
	// site.surl_addr
	// site.url_addr
	public static function init($config_path)
	{
		debug::on(false);
		global $di; // required for include files

		self::$config_path = $config_path;

		// include load.php first
		include($config_path.'load.php');

		$namespaces = new ConfigNamespaces();
		$namespaces->load_namespaces($config_path);
		self::$namespace_prefixes = $namespaces->namespace_prefixes;
//		include($config_path.'namespaces.php');

		if (is_array(self::$namespace_prefixes))
		{
			self::set('site.namespace_prefixes', self::$namespace_prefixes);
		}


		// set site.domain_name
		self::set('site.domain_name', $_SERVER['HTTP_HOST']);

		// set site.ip_address
		if (!empty($_SERVER['SERVER_ADDR']))
		{
			self::set('site.ip_address', $_SERVER["SERVER_ADDR"]);
		}
		elseif (!empty($_SERVER['LOCAL_ADDR']))
		{
			self::set('site.ip_address', $_SERVER["LOCAL_ADDR"]);
		}

		// set site.is_prod
		// set site.is_stag
		// set site.is_dev
		self::determine_site();

		// set site.root_dir
		$root_dirs = self::get('site.root_dirs');
		//debug::variable($root_dirs, 'root_dirs');
		$root_dir = (isset($root_dirs[self::get('site.domain_name')])) ? $root_dirs[self::get('site.domain_name')] : '';
		//debug::variable($root_dir, 'root_dir');
		self::set('site.root_dir', $root_dir);

		// set site.has_ssl
		$has_ssl_list = self::get('site.has_ssl_list');
		$has_ssl = isset($has_ssl_list[self::get('site.domain_name')]) ? $has_ssl_list[self::get('site.domain_name')] : '';
		self::set('site.has_ssl', $has_ssl);

		// set site.surl_addr, site.url_addr
		self::set('site.surl_addr', (self::get('site.has_ssl') ? 'https://' : 'http://'). self::get('site.domain_name'));
		self::set('site.url_addr', 'http://'. self::get('site.domain_name'));
		debug::variable(self::get('site.surl_addr'));

		// THIS IS NOW BEING DONE IN FRONT.PHP AND GLOBAL.PHP
		// load facade class definitions (need access to $di)
		// we need to load the facade classes first
		//$framework_classes_file = Config::get('site.framework_dir').'_config'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'framework_classes.php';
		//debug::variable($framework_classes_file);
		//require_once($framework_classes_file);

		$config_dirs = Config::get('site.config_dirs');
		//debug::variable($config_dirs, 'config_dirs');

		//debug::constant(Config::get('site'), 'site');

		if (is_array($config_dirs))
		{
			foreach ($config_dirs as $config_dir)
			{
				$action_files = self::get_config_files($config_dir, 'actions');
				$block_files = self::get_config_files($config_dir, 'blocks');
				$class_files = self::get_config_files($config_dir, 'classes');
				$controller_files =  self::get_config_files($config_dir, 'controllers');
				$gateway_files = self::get_config_files($config_dir, 'gateways');
				$path_files = self::get_config_files($config_dir, 'paths');
				$setting_files = self::get_config_files($config_dir, 'settings');
				$table_files = self::get_config_files($config_dir, 'tables');
				$functions_files = self::get_config_files($config_dir, 'functions');

				//debug::variable($action_files, 'action_files');
				//debug::variable($block_files, 'block_files');
				//debug::variable($class_files, 'class_files');
				//debug::variable($controller_files, 'controller_files');
				//debug::variable($gateway_files, 'gateway_files');
				//debug::variable($path_files, 'path_files');
				//debug::variable($setting_files, 'setting_files');
				//debug::variable($table_files, 'table_files');
				//debug::variable($functions_files, 'functions_files');


				if (is_array($action_files))
				{
					foreach ($action_files as $action_file)
					{
						include($action_file);
					}
				}
				if (is_array($block_files))
				{
					foreach ($block_files as $block_file)
					{
						include($block_file);
					}
				}
				if (is_array($class_files))
				{
					foreach ($class_files as $class_file)
					{
						//if ($class_file <> $framework_classes_file)
						//{
							include($class_file);
						//}
					}
				}
				if (is_array($controller_files))
				{
					foreach ($controller_files as $controller_file)
					{
						include($controller_file);
					}
				}
				if (is_array($gateway_files))
				{
					foreach ($gateway_files as $gateway_file)
					{
						include($gateway_file);
					}
				}
				if (is_array($path_files))
				{
					foreach ($path_files as $path_file)
					{
						include($path_file);
					}
				}
				if (is_array($setting_files))
				{
					foreach ($setting_files as $setting_file)
					{
						include($setting_file);
					}
				}
				if (is_array($table_files))
				{
					foreach ($table_files as $table_file)
					{
						include($table_file);
					}
				}
				// load functions last so that we have access to paths.
				if (is_array($functions_files))
				{
					foreach ($functions_files as $functions_file)
					{
						include($functions_file);
					}
				}
			}
		}


		// load dictionary definitions
		$dictionary_files = File::get_dir_file_list(APP_ROOT_DIR.'/_config/dictionary/');
		if (is_array($dictionary_files))
		{
			foreach ($dictionary_files as $file)
			{
				require_once(APP_ROOT_DIR.'/_config/dictionary/'.$file);
			}
		}

		//debug::variable(self::$values, 'this->values');
	}

	private static function determine_site()
	{
		debug::on(false);
		//debug::constant(self::get('site.prod_domains'), 'site.prod_domains');
		//debug::constant(self::get('site.stag_domains'), 'site.stag_domains');
		//debug::constant(self::get('site.dev_domains'), 'site.dev_domains');

		self::set('site.is_prod', false);
		self::set('site.is_stag', false);
		self::set('site.is_dev', false);

		self::set('site.domain_name', self::_determine_site(self::get('site.prod_domains')));
		if (self::get('site.domain_name') <> '')
		{
			self::set('site.is_prod', true);
		}
		else
		{
			self::set('site.domain_name', self::_determine_site(self::get('site.stag_domains')));
			if (self::get('site.domain_name') <> '')
			{
				self::set('site.is_stag', true);
			}
			else
			{
				self::set('site.domain_name', self::_determine_site(self::get('site.dev_domains')));
				if (self::get('site.domain_name') <> '')
				{
					self::set('site.is_dev', true);
				}
			}
		}

		//debug::string("After set");
		//debug::constant(self::get('site.is_prod'), 'site.is_prod');
		//debug::constant(self::get('site.is_stag'), 'site.is_stag');
		//debug::constant(self::get('site.is_dev'), 'site.is_dev');
	}

	private static function _determine_site($site_domains)
	{
		global $argc;
		global $argv;

		debug::on(false);
		//debug::variable($argc, 'argc');
		//debug::variable($argv, 'argv');
		//debug::variable($site_domains, 'site_domains');

		if ($argc > 1)
		{
			// determine site from command line request
			$working_dir = getcwd();
			//debug::variable($working_dir, 'working_dir');
			if (!empty($working_dir) && !empty($site_domains))
			{
				if (is_array(self::get('site.root_dirs')))
				{
					foreach (self::get('site.root_dirs') as $site_domain => $site_dir)
					{
						$pos = strpos($working_dir, $site_dir);
						if ($pos !== false)
						{
							if (in_array($site_domain, $site_domains))
							{
								return $site_domain;
							}
						}
					}
				}
			}
			return false;
		}
		else
		{
			//debug::variable($_SERVER['HTTP_HOST'], '_SERVER[HTTP_HOST]');
			// determine site from browser request
			if (!empty($_SERVER['HTTP_HOST']))
			{
				if (!empty($site_domains) && in_array($_SERVER['HTTP_HOST'], $site_domains))
				{
					//debug::string("site matched");
					return $_SERVER['HTTP_HOST'];
				}
				else
				{
					//debug::string("site NOT matched");
					return false;
				}
			}
			else
			{
				//debug::string("HTTP HOST not set");
				return false;
			}
		}
	}


	public static function check_required()
	{
		debug::on(false);

		$config_dirs = Config::get('site.config_dirs');
		debug::variable($config_dirs, 'config_dirs');

		if (is_array($config_dirs))
		{
			foreach ($config_dirs as $config_dir)
			{
				$required_files = self::get_required_files($config_dir);
				debug::variable($required_files, 'required_files');

				if (is_array($required_files))
				{
					foreach ($required_files as $required_file)
					{
						include($required_file);
					}
				}
			}
		}

		//----------------------------
		// CHECK REQUIRED SETTINGS
		//----------------------------
		$required_settings = Config::get('_required_settings');
		//debug::variable($required_settings, 'required_settings');

		$optional_packages = Config::get('_optional_packages');
		debug::variable($optional_packages, 'optional_packages');

		$missing_settings = array();
		$extra_settings = array();
		if (is_array($required_settings))
		{
			foreach ($required_settings as $required_pkg => $required_pkg_settings)
			{
				// don't process optional packages that don't have any values
				$ok_to_process = true;
				if (in_array($required_pkg, $optional_packages))
				{
					if (empty(self::$values[$required_pkg]))
					{
						$ok_to_process = false;
					}
				}
				debug::variable($ok_to_process, 'ok_to_process');

				if ($ok_to_process)
				{
					debug::variable($required_pkg, 'required_pkg');
					//debug::variable($required_pkg_settings, 'required_pkg_settings');

					$config_settings = (isset(self::$values[$required_pkg])) ? self::$values[$required_pkg] : array();
					//debug::variable($config_settings, 'config_settings');

					$missing_settings[$required_pkg] = Arrays::array_diff_key_recursive($required_pkg_settings, $config_settings);
					debug::variable($missing_settings, 'missing_settings');

					$extra_settings[$required_pkg] = Arrays::array_diff_key_recursive($config_settings, $required_pkg_settings);
					debug::variable($extra_settings, 'extra_settings');
				}
			}
		}

		//----------------------------
		// CHECK REQUIRED NAMESPACES
		//----------------------------
		$required_namespaces = Config::get('_required_namespaces');
		debug::variable($required_namespaces, 'required_namespaces');

		debug::variable(self::$namespace_prefixes, 'self::namespace_prefixes');

		$missing_namespaces = array();
		if (is_array($required_namespaces))
		{
			foreach ($required_namespaces as $required_namespace)
			{
				debug::variable($required_namespace, 'required_namespace');
				if (!isset(self::$namespace_prefixes[$required_namespace]))
				{
					$missing_namespaces[] = $required_namespace;
				}
			}
		}
		debug::variable($missing_namespaces, 'missing_namespaces');

		//----------------------------
		// CHECK EXTRA NAMESPACES
		//----------------------------
		$extra_namespaces = array();
		if (is_array(self::$namespace_prefixes))
		{
			foreach (self::$namespace_prefixes as $namespace_prefix => $value)
			{
				debug::variable($namespace_prefix, 'namespace_prefix');
				if (!in_array($namespace_prefix, $required_namespaces))
				{
					$extra_namespaces[] = $namespace_prefix;
				}
			}
		}
		debug::variable($extra_namespaces, 'extra_namesapces');

		return array(
			'missing_namespaces' => $missing_namespaces,
			'extra_namespaces' => $extra_namespaces,
			'missing_settings' => $missing_settings,
			'extra_settings' => $extra_settings,
		);
	}


	public static function get_required_files($config_dir)
	{
		debug::on(false);
		debug::string('get_config_files()');
		debug::variable($config_dir, 'config_dir');

		$folder = 'settings';
		debug::variable($folder, 'folder');

		$dir_path = $config_dir.$folder;
		debug::variable($dir_path, 'dir_path');

		$dir_file_list = array();
		if (is_dir($dir_path))
		{
			$dir_file_list = @scandir($dir_path);
			debug::variable($dir_file_list, 'dir_file_list');
		}

		$file_list = array();
		if (is_array($dir_file_list))
		{
			foreach ($dir_file_list as $file)
			{
				debug::variable($file, 'file');
				if ($file <> '.' and $file <> '..')
				{
					// file must end with _rquired.php, eg: cms_required.php
					$ends_with = '_required.php';
					debug::variable($ends_with, 'ends_with');

					if (strpos($file, $ends_with) !== false)
					{
						$file_list[] = $dir_path.DIRECTORY_SEPARATOR.$file;
					}
				}
			}
		}
		debug::variable($file_list, 'file_list');

		return $file_list;
	}


}

class ConfigNamespaces
{
	public $namespace_prefixes = array();

	public function load_namespaces($config_path)
	{
		include($config_path.'namespaces.php');
	}
}

?>