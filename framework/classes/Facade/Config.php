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
	public static $namespace_prefixes = array();

	public static function get($key='')
	{
		debug::on(false);
		@list($pkg, $var1, $var2, $var3, $var4) = @explode('.', $key);
		//debug::variable($pkg, 'pkg');
		//debug::variable($var1, 'var1');
		//debug::variable($var2, 'var2');
		//debug::variable($var3, 'var3');
		//debug::variable($var4, 'var4');

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

					if (strpos($file, $ends_with) !== false)
					{
						$file_list[] = $dir_path.DIRECTORY_SEPARATOR.$file;
					}
				}
			}
		}
		//debug::variable($file_list, 'file_list');

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

		// set site.surl_addr, site.url_addr
		self::set('site.surl_addr', (self::get('site.has_ssl') ? 'https://' : 'http://'). self::get('site.domain_name'));
		self::set('site.url_addr', 'http://'. self::get('site.domain_name'));
		debug::variable(self::get('site.surl_addr'));

		$config_dirs = Config::get('site.config_dirs');
		//debug::variable($config_dirs, 'config_dirs');

		//debug::constant(Config::get('site'), 'site');

		if (is_array($config_dirs))
		{
			foreach ($config_dirs as $config_dir)
			{
				$path_files = self::get_config_files($config_dir, 'paths');
				//debug::variable($path_files, 'path_files');

				$setting_files = self::get_config_files($config_dir, 'settings');
				//debug::variable($setting_files, 'setting_files');

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
			}
		}

		//debug::variable(self::$values, 'this->values');
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