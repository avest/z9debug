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

//use phpbrowscap\Exception;

class Autoloader
{
	public $namespace_prefixes = array();
	public $loaded_files = array();
	public $config_class_file = '';
	public $config_class_loaded = false;

	public function init($namespaces_file)
	{
		debug::on(false);
		//debug::string('init()');

		$this->config_class_file = APP_ROOT_DIR.'/framework/classes/Facade/Config.php';

		//debug::variable($namespaces_file, 'namespaces_file');
		include($namespaces_file);
	}

	// an instance method
	public function load($class)
	{
		//debug::enabled(true);
		//debug::suppress_output(true);
		//debug::on(false);

		//debug::default_limit(100);
		//debug::string('load()');
		//debug::variable($class);
		//debug::constant(debug_backtrace(), 'backtrace');

		//debug::variable($this->namespace_prefixes);

		// strip off any leading namespace separator from PHP 5.3
		$class = ltrim($class, '\\');
		//debug::variable($class);

		$new_class = $class;

		$namespace_prefixes = $this->namespace_prefixes;

		$default_namespace_dir = '';
		if (isset($this->namespace_prefixes['*']['classes_dir']))
		{
			$default_namespace_dir = $this->namespace_prefixes['*']['classes_dir'];
			unset($namespace_prefixes['*']);
		}
		//debug::variable($default_namespace_dir);

		$namespace_path = $default_namespace_dir;
		if (is_array($namespace_prefixes))
		{
			foreach ($namespace_prefixes as $namespace_prefix => $namespace_prefix_path)
			{
				//debug::variable($namespace_prefix);
				if (strpos($new_class, $namespace_prefix) === 0)
				{
					//debug::string('found match');
					$namespace_path = $namespace_prefix_path['classes_dir'];

					// update the class to not have the prefix

					$new_class = substr($new_class, strlen($namespace_prefix) + 1);
					//debug::variable($new_class);

					// only match one time!
					break;
				}
			}
		}
		//debug::variable($namespace_path);

		// the eventual file path
		$subpath = '';

		// is there a PHP 5.3 namespace separator?
		$pos = strrpos($new_class, '\\');
		//debug::variable($pos);

		if ($pos !== false)
		{
			// convert namespace separators to directory separators
			$ns = substr($new_class, 0, $pos);
			//debug::variable($ns);
			$subpath = str_replace('\\', DIRECTORY_SEPARATOR, $ns) . DIRECTORY_SEPARATOR;
			//debug::variable($subpath);
			// remove the namespace portion from the final class name portion
			$new_class = substr($new_class, $pos + 1);
			//debug::variable($new_class);
		}

		// convert underscores in the class name to directory separators
		$subpath .= $new_class;
		if (false)
		{
			$subpath .= str_replace('_', DIRECTORY_SEPARATOR, $new_class);
		}
		//debug::variable($subpath);

		// the path to our central class directory location
		//$dir = APP_ROOT_DIR.'/classes';
		// $_SERVER['DOCUMENT_ROOT'] doesn't work for unit testing...
		//$dir = dirname( __FILE__ ).'/..';
		$dir = $namespace_path;
		$dir = realpath($dir);
		//debug::variable($dir);

		// prefix with the central directory location and suffix with .php,
		// then require it.
		$file = $dir . DIRECTORY_SEPARATOR . $subpath . '.php';
		//debug::variable($file);

		//------------------------------------------
		// check for autoloader.exclude_dirs
		//------------------------------------------
		$is_included = true;
		// THIS WAS AN ATTEMPT TO FILTER AMAZON CLASSES, BUT IT DIDN'T WORK AS INTENDED...
		//if ($this->config_class_loaded)
		//{
		//	$exclude_dirs = Config::get('autoloader.exclude_dirs');
		//	debug::variable($exclude_dirs, 'exclude_dirs');
		//
		//	$relative_file = $this->remove_leading($file, APP_ROOT_DIR);
		//	debug::variable($relative_file, 'relative_file');
		//
		//	if (is_array($exclude_dirs))
		//	{
		//		foreach ($exclude_dirs as $exclude_dir)
		//		{
		//			if ($this->starts_with($relative_file, $exclude_dir))
		//			{
		//				$is_included = false;
		//				break;
		//			}
		//		}
		//	}
		//}
		//debug::variable($is_included, 'is_included');


		// We need to be able to detect fatal errors when including a file.
		// (so they are not hidden)
		// But, we also need to detect if the file loaded...
		// We are pretty much stuck with testing to see if the file exists.
		// Note: do not use @ in front of include()
		if ($is_included)
		{
			$is_file_included = false;
			if (is_file($file))
			{
				//debug::string('class file found');
				$is_file_included = include($file);
				//debug::variable($is_file_included);
				//debug::string('class file loaded');
				$this->loaded_files[] = $file;
				//debug::variable($this->loaded_files);

				// check if we just loaded the Config class
				if ($file == $this->config_class_file)
				{
					$this->config_class_loaded = true;
					//debug::variable($this->config_class_loaded);
				}
			}

			// test that class name was loaded
			$confirm_class_name = '\\'.$class;
			//debug::variable($confirm_class_name);

			if (!class_exists($confirm_class_name, false) && !interface_exists($confirm_class_name, false))
			{
				if (Config::get('site.is_dev'))
				{
					debug::enabled(true);
					debug::suppress_output(false);
					debug::on();
					debug::string('ERROR: class name '.$confirm_class_name.' not found after load');
					debug::variable($class);
					debug::variable($file);
					debug::stack_trace();
				}
				else
				{
					debug::enabled(true);
					debug::suppress_output(true);
					debug::on();
					debug::string('ERROR: class name '.$confirm_class_name.' not found after load');
					debug::variable($class);
					debug::variable($file);
					debug::stack_trace();
				}
				echo "<style>";
				echo "BODY {";
				echo "padding:20px;";
				echo "font-size:14px;";
				echo "font-family: Arial;";
				echo "}";
				echo "</style>";
				echo "<h3>ERROR</h3>";
				echo 'Class name not found after class file load.<br><br>';
				echo "Class: ".$class."<br><br>";
				echo "File: ".$file."<br><br>";
				exit();
			}

			if (!$is_file_included)
			{
				if (Config::get('site.is_dev'))
				{
					debug::enabled(true);
					debug::suppress_output(false);
					debug::on();
					debug::string('ERROR: Could not autoload file.');
					debug::variable($file);
					debug::stack_trace();
				}
				echo "<style>";
				echo "BODY {";
				echo "padding:20px;";
				echo "font-size:14px;";
				echo "font-family: Arial;";
				echo "}";
				echo "</style>";
				echo "<h3>ERROR</h3>";
				echo "Could not autoload file.<br><br>";
				echo "File: ".$file."<br><br>";
				exit();
			}
		}


	}

	//--------------------------------------------------------------
	// SUPPORTING METHODS SO WE DON'T NEED TO RELY ON STR FACADE
	// WHICH MAY NOT BE LOADED YET...
	//--------------------------------------------------------------
	private function remove_leading($input_string, $match_string)
	{
		if ($this->starts_with($input_string, $match_string))
		{
			return $this->mid($input_string, strlen($match_string)+1);
		}
		else
		{
			return $input_string;
		}
	}

	private function starts_with($input_string, $match_string)
	{
		$match_len = strlen($match_string);
		if ($match_string == $this->left($input_string, $match_len))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function left($input_string, $str_length)
	{
		$output_string = '';
		if (!empty($input_string))
		{
			$output_string = substr($input_string, 0, $str_length);
		}
		return $output_string;
	}

	private function mid($str, $start, $howManyCharsToRetrieve = 0)
	{
		$return_value = '';
		if (!empty($str))
		{
			$start--;
			if ($howManyCharsToRetrieve === 0)
			{
				$howManyCharsToRetrieve = strlen($str) - $start;
			}

			$return_value = substr($str, $start, $howManyCharsToRetrieve);
			if (empty($return_value) && $return_value <> '0')
			{
				$return_value = '';
			}
		}

		return $return_value;
	}

}

?>