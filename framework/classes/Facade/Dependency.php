<?php
//===================================================================
// Z9 Framework
//===================================================================
// Dependency.php
// --------------------
//       Date Created: 2005-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

/*
 *  Container for action classes
 */

namespace Facade;

use debug;
use Mlaphp\Di;

class Dependency
{

	private static $di;

	public static $dependency = array();

	public function __construct(Di $di = null)
	{
		debug::on(false);
		debug::string('__construct()');
		//debug::variable($di, 'di');
		self::$di = $di;
	}

	public static function inject($di_name)
	{
		debug::on(false);
		debug::variable($di_name);
		//debug::stack_trace();

		$namespace_prefixes = Config::get('site.namespace_prefixes');
		debug::variable($namespace_prefixes);

		$prefix = '';
		if (is_array($namespace_prefixes))
		{
			foreach ($namespace_prefixes as $namespace_prefix => $namespace_prefix_path)
			{
				if (strpos($di_name, $namespace_prefix) === 0)
				{
					debug::string('found match');
					$prefix = $namespace_prefix;

					// update the class to not have the prefix
					$di_name = substr($di_name, strlen($namespace_prefix) + 1);
					debug::variable($di_name);

				}

			}
		}
		debug::variable($prefix);

		$dependency_class = $prefix.$di_name;
		debug::variable($dependency_class);

		$return_class = false;
		//print_r(self::$di);
		if (!isset(self::$dependency[$dependency_class]))
		{
			//debug::string('calling di->get');
			$return_class = self::$di->get($dependency_class);
		}
		//debug::string('return_class set');

		return $return_class;
	}


	public static function new_instance($di_name)
	{
		debug::on(false);
		debug::variable($di_name);
		//debug::stack_trace();

		$namespace_prefixes = Config::get('site.namespace_prefixes');
		debug::variable($namespace_prefixes);

		$prefix = '';
		if (is_array($namespace_prefixes))
		{
			foreach ($namespace_prefixes as $namespace_prefix => $namespace_prefix_path)
			{
				if (strpos($di_name, $namespace_prefix) === 0)
				{
					debug::string('found match');
					$prefix = $namespace_prefix;

					// update the class to not have the prefix
					$di_name = substr($di_name, strlen($namespace_prefix) + 1);
					debug::variable($di_name);

				}

			}
		}
		debug::variable($prefix);

		$dependency_class = $prefix.$di_name;
		debug::variable($dependency_class);

		$return_class = false;
		//print_r(self::$di);
		if (!isset(self::$dependency[$dependency_class]))
		{
			//debug::string('calling di->get');
			$return_class = self::$di->newInstance($dependency_class);
		}
		//debug::string('return_class set');

		return $return_class;
	}



}


?>