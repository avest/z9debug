<?php
//===================================================================
// Z9 Framework
//===================================================================
// Action.php
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

class Action
{

	public static $di;

	public static $action = array();

	public function __construct(Di $di = null)
	{
		debug::on(false);
		//debug::variable($di, 'di');
		self::$di = $di;
	}

	public static function _($class_path)
	{
		debug::on(false);
		//debug::variable($class_path, 'class_path');

		$namespace_prefixes = Config::get('site.namespace_prefixes');
		//debug::variable($namespace_prefixes, 'namespace_prefixes');

		$prefix = '';
		if (is_array($namespace_prefixes))
		{
			foreach ($namespace_prefixes as $namespace_prefix => $namespace_prefix_path)
			{
				if (strpos($class_path, $namespace_prefix) === 0)
				{
					//debug::string('found match');
					$prefix = $namespace_prefix;

					// update the class to not have the prefix
					$class_path = substr($class_path, strlen($namespace_prefix) + 1);
					//debug::variable($class_path, 'class_path');

				}

			}
		}
		//debug::variable($prefix, 'prefix');
		//debug::variable($class_path, 'class_path');

		$action_class = $prefix.'\\Action\\'.$class_path.'Action';
		//debug::variable($action_class, 'action_class');

		//debug::variable(self::$di, 'self::di');
		if (!isset(self::$action[$action_class]))
		{
			self::$action[$action_class] = self::$di->get($action_class);
			//debug::variable(self::$action, 'self::action');
		}

		return self::$action[$action_class];

	}

}


?>