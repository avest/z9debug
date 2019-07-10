<?php
//===================================================================
// Z9 Framework
//===================================================================
// Php.php
// --------------------
//       Date Created: 2005-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

// function turn_magic_quotes_gpc_off()


namespace Facade;

use debug;
use Facade\Str;

class Php
{
	public function _construct()
	{
	}

	public static function version()
	{
		debug::on(false);

		$version = phpversion();
		debug::variable($version);

		$version = explode('.', $version);
		debug::variable($version);

		$version = array(
			'PHP_VERSION' => $version[0].'.'.$version[1].'.'.$version[2],
			'PHP_MAJOR_VERSION' => $version[0],
			'PHP_MINOR_VERSION' => $version[1],
			'PHP_RELEASE_VERSION' => $version[2],
		);
		debug::variable($version);

		return $version;
	}

	public static function parse_class_name($class)
	{
		debug::on(false);
		debug::variable($class);

		$namespace = '';
		$class_name = '';

		if (Str::in_str($class, '\\'))
		{
			$parts = explode('\\', $class);
			debug::variable($parts);

			if (is_array($parts))
			{
				$parts_count = count($parts);
				debug::variable($parts_count);

				if ($parts_count > 1)
				{
					$class_name = $parts[$parts_count - 1];
					debug::variable($class_name);

					unset($parts[$parts_count - 1]);
				}
				else
				{
					$class_name = $class;
					debug::variable($class_name);
				}
			}

			$namespace = implode('\\', $parts);
		}
		debug::variable($namespace);

		return array(
			'namespace' => $namespace,
			'class_name' => $class_name,
		);
	}

}

?>