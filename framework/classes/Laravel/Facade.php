<?php

// modified version of Laravel's Facade class

namespace Laravel;

use debug;
use Mlaphp\Di;

class Facade
{
	protected static $resolvedInstance;

	protected static $di;

	// $di already had the class definitions loaded
	public function __construct(Di $di)
	{
		debug::on(false);
		//debug::variable($di, 'di');
		static::$di = $di;
		//debug::variable(static::$di, 'di');
	}

	public static function __callStatic($method, $args)
	{
		debug::on(false);
		//debug::variable($method, 'method');
		//debug::variable($args, 'args');

		$instance = static::getFacadeRoot();
		//debug::variable($instance, 'instance');

		switch (count($args))
		{
			case 0:
				return $instance->$method();

			case 1:
				return $instance->$method($args[0]);

			case 2:
				return $instance->$method($args[0], $args[1]);

			case 3:
				return $instance->$method($args[0], $args[1], $args[2]);

			case 4:
				return $instance->$method($args[0], $args[1], $args[2], $args[3]);

			default:
				return call_user_func_array(array($instance, $method), $args);
		}
	}

	public static function getFacadeRoot()
	{
		debug::on(false);

		$facade_accessor = static::getFacadeAccessor();
		//debug::variable($facade_accessor, 'facade_accessor');

		$facade_instance = static::resolveFacadeInstance($facade_accessor);
		//debug::variable($facade_instance, 'facade_instance');

		return $facade_instance;
	}

	// this only gets called if extending class doesn't redefine this method
	protected static function getFacadeAccessor()
	{
		throw new RuntimeException("Facade does not implement getFacadeAccessor method.");
	}

	protected static function resolveFacadeInstance($name)
	{
		debug::on(false);
		//debug::variable($name, 'name');

		if (is_object($name))
		{
			//debug::string("is_object() found.");
			return $name;
		}

		if (isset(static::$resolvedInstance[$name]))
		{
			//debug::string("resolvedInstance[] found.");
			return static::$resolvedInstance[$name];
		}

		//debug::string("generating instance");

		static::$resolvedInstance[$name] = static::$di->get($name);

		return static::$resolvedInstance[$name];
	}
}

?>