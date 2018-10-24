<?php
//===================================================================
// Library
//===================================================================
// Hooks.php
// --------------------
// A set of generic set of functions for implementing hooks.
//
//       Date Created: 2016-04-10
//    Original Author: Allan Vest <avest@allmerchants.com>
// Additional Authors:
//
// Revision History:
// -------------------
// 2016-04-10, AVest, Initial Version
//
// Copyright 2016, AllMerchants, L.L.C.
// All Rights Reserved
// http://www.allmerchants.com
//
// Warning: This computer program and related user documentation
// are protected under copyright law and international treaties.
// Unauthorized reproduction or distribution of this program, or any
// portion of it, may result in severe civil and criminal penalties,
// and will be prosecuted to the maximum extent possible under
// the law.
//
// See the LICENSE.txt file included with this program for additional
// licensing information.
//===================================================================
namespace Z9\Framework;

use debug;
use Facade\Config;

class Hooks
{
	public $hooks = array();
	private $di = null;

	public function __construct($di)
	{
		$this->di = $di;
	}

	// register actions for a given hook name
	public function add_action($hook, $class, $method, $priority, $args_count)
	{
		debug::on(false);
		debug::variable($hook, 'hook');
		debug::variable($class, 'class');
		debug::variable($method, 'method');
		debug::variable($priority, 'priority');
		debug::variable($args_count, 'args_count');

		// TODO - need to make sure that a previous priority index isn't overwritten...
		$this->hooks[$hook][$priority] = array(
			'class' => $class,
			'method' => $method,
			'args_count' => $args_count,
		);
		debug::variable($this->hooks, 'this->hooks');
	}

	public function do_action($hook)
	{
		debug::on(false);
		debug::string('do_action()');
		debug::variable($hook, 'hook');

		$arguments = func_get_args();
		debug::variable($arguments, 'arguments');

		$return = array();

		if (isset($this->hooks[$hook]))
		{
			if (is_array($this->hooks[$hook]))
			{
				ksort($this->hooks[$hook]);
				foreach ($this->hooks[$hook] as $key => $action)
				{
					debug::variable($key, 'key');
					debug::variable($action, 'action');

					$class_name = $action['class'];
					debug::variable($class_name, 'class_name');

					$method = $action['method'];
					debug::variable($method, 'method');

					$args_count = $action['args_count'];
					debug::variable($args_count, 'args_count');

					if (!empty($class_name) && !empty($method))
					{
						$class = $this->di->get($class_name);
						//debug::variable($class, 'class');

						switch ($args_count)
						{
							case 0:
								$result = $class->{$method}();
								$return[] = array(
									'class' => $class_name,
									'method' => $method,
									'result' => $result,
								);
								break;
							case 1:
								$result = $class->{$method}(
									$arguments[1]
								);
								$return[] = array(
									'class' => $class_name,
									'method' => $method,
									'result' => $result,
								);
								break;
							case 2:
								$result = $class->{$method}(
									$arguments[1],
									$arguments[2]
								);
								$return[] = array(
									'class' => $class_name,
									'method' => $method,
									'result' => $result,
								);
								break;
							case 3:
								$result = $class->{$method}(
									$arguments[1],
									$arguments[2],
									$arguments[3]
								);
								$return[] = array(
									'class' => $class_name,
									'method' => $method,
									'result' => $result,
								);
								break;
							case 4:
								$result = $class->{$method}(
									$arguments[1],
									$arguments[2],
									$arguments[3],
									$arguments[4]
								);
								$return[] = array(
									'class' => $class_name,
									'method' => $method,
									'result' => $result,
								);
								break;
							case 5:
								$result = $class->{$method}(
									$arguments[1],
									$arguments[2],
									$arguments[3],
									$arguments[4],
									$arguments[5]
								);
								$return[] = array(
									'class' => $class_name,
									'method' => $method,
									'result' => $result,
								);
								break;
						}
					}
				}
			}
		}

		debug::variable($return, 'return');
		return $return;
	}

	// load hooks from _config folders...
	public function init()
	{
		debug::on(false);
		//debug::string('init()');

		$config_dirs = Config::get('site.config_dirs');
		//debug::variable($config_dirs, 'config_dirs');

		if (is_array($config_dirs))
		{
			foreach ($config_dirs as $config_dir)
			{
				//debug::variable($config_dir, 'config_dir');

				$hook_files = $this->get_config_files($config_dir, 'hooks');
				//debug::variable($hook_files, 'hook_files');

				if (is_array($hook_files))
				{
					foreach ($hook_files as $hook_file)
					{
						include($hook_file);
					}
				}
			}
		}
	}

	public function get_config_files($config_dir, $folder)
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

					if (strpos($file, $ends_with) !== false || $file == 'known_pages.php')
					{
						$file_list[] = $dir_path.DIRECTORY_SEPARATOR.$file;
					}
				}
			}
		}
		//debug::variable($file_list, 'file_list');
		return $file_list;
	}

}