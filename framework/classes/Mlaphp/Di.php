<?php
/**
 * This file is part of "Modernizing Legacy Applications in PHP".
 *
 * @copyright 2014 Paul M. Jones <pmjones88@gmail.com>
 * @license http://opensource.org/licenses/bsd-license.php BSD
 */
namespace Mlaphp;

//use UnexpectedValueException;
use debug;

/**
 * A dependency injection container.
 */
class Di
{
	// test
	/**
	* A registry of callables to create object instances.
	*
	* @var array
	*/
	protected $callables = array();

	/**
	* A registry of shared instances created by the callables.
	*
	* @var array
	*/
	protected $instances = array();

	/**
	* Variables used as parameters; accessed as magic properties.
	*
	* @var array
	*/
	protected $variables = array();

	protected $closures = array();

	/**
	* Constructor.
	*
	* @param array $variables A an existing array of variables to be used as
	* magic properties, typically $GLOBALS.
	*/
	public function __construct(array &$variables = array())
	{
		$this->variables = $variables;
	}

	/**
	* Gets a magic property.
	*
	* @param string $key The property name.
	* @return null
	*/
	public function __get($key)
	{
		return $this->variables[$key];
	}

	/**
	* Sets a magic property.
	*
	* @param string $key The property name.
	* @param mixed $val The property value.
	* @return null
	*/
	public function __set($key, $val)
	{
		$this->variables[$key] = $val;
	}

	/**
	* Is a magic property set?
	*
	* @param string $key The property name.
	* @return bool
	*/
	public function __isset($key)
	{
		return isset($this->variables[$key]);
	}

	/**
	* Unsets a magic property.
	*
	* @param string $key The property name.
	* @return bool
	*/
	public function __unset($key)
	{
		unset($this->variables[$key]);
	}

	/**
	* Sets a callable to create an object by name; removes any existing
	* shared instance under that name.
	*
	* @param string $name The object name.
	* @param callable $callable A callable that returns an object.
	* @return null
	*/
	public function set($name, $callable)
	{
		$name = ltrim($name, '\\');
		$this->callables[$name] = $callable;
		//echo"callabes=<pre>";print_r($this->callables);echo"</pre><br>";
		unset($this->instances[$name]);
	}

	public function set_closures($closures)
	{
		$this->closures = array_merge($this->closures, $closures);
	}

	/**
	* Is a named callable defined?
	*
	* @return bool
	*/
	public function has($name)
	{
		debug::on(false);
		debug::default_limit(100);
		//if ($name == 'Controller\ResetPassword') { debug::on(); }
		//if (debug::is_on()) { echo '-has-';print_r($name);echo "\n"; }

		$name = ltrim($name, '\\');
		debug::variable($name);

		debug::variable($this->callables);

		$is_defined = isset($this->callables[$name]);
		debug::variable($is_defined);

		//-----------------------------------------------------------
		// Dynamically create closure from $this->closures settings
		//-----------------------------------------------------------
		if (!$is_defined)
		{
		 	if (isset($this->closures[$name]))
			{
				//-----------------------------------------------------
				// class dependencies are defined in a config file...
				//-----------------------------------------------------
				debug::variable($this->closures[$name], 'this->closures['.$name.']');

				$str = 'global $di;'."\n";
				$str .= 'return new '.$name.'('."\n";
				if (is_array($this->closures[$name]))
				{
					$param_count = count($this->closures[$name]);
					debug::variable($param_count);
					foreach ($this->closures[$name] as $param_index => $param)
					{
						//debug::variable($param_index, 'param_index');
						$str .= '$di->get("'.$param.'")'.(($param_index + 1 < $param_count) ? ',' : '')."\n";
					}
				}
				$str .= ');'."\n";
				debug::variable($str);
			}
			else
			{
				//---------------------------------------------------------
				// class dependencies are NOT defined in a config file...
				//---------------------------------------------------------
				$str = 'global $di;'."\n";
				$str .= 'return new '.$name.'('."\n";
				$str .= ');'."\n";
				debug::variable($str);
			}

			// NOTE: create_function is going to be deprecated in PHP 7.2
			$closure = create_function('', $str);
			debug::variable($closure);
			$this->set($name, $closure);

			$is_defined = isset($this->callables[$name]);
			debug::variable($is_defined);
		}

		return $is_defined;
	}

	/**
	* Gets a shared instance by object name; if it has not been created yet,
	* its callable will be invoked and the instance will be retained.
	*
	* @param string $name The name of the shared instance to retrieve.
	* @return object The shared object instance.
	*/
	public function get($name)
	{
		debug::on(false);
		debug::variable($name);

		$name = ltrim($name, '\\');
		debug::variable($name);
		if (! isset($this->instances[$name]))
		{
			debug::string('new instance');
			$this->instances[$name] = $this->newInstance($name);
		}
		else
		{
			debug::string('existing instance');
		}
		//if (debug::is_on()) { echo '-get-';print_r($this->instances[$name]);echo "\n"; }
		$return_instance = $this->instances[$name];

		return $return_instance;
	}

	public function get_instance_names()
	{
		debug::on(false);
		$return = array_keys($this->instances);
		debug::variable($return);
		return $return;
	}

	public function get_closure_names()
	{
		debug::on(false);
		$return = array_keys($this->closures);
		debug::variable($return);
		return $return;
	}

	public function get_callable_names()
	{
		debug::on(false);
		$return = array_keys($this->callables);
		debug::variable($return);
		return $return;
	}

	public function is_instance($name)
	{
		debug::on(false);
		debug::variable($name);

		$name = ltrim($name, '\\');
		debug::variable($name);

		$is_instance = false;
		if (isset($this->instances[$name]))
		{
			$is_instance = true;
		}
		debug::variable($is_instance);

		return $is_instance;
	}


	/**
	* Returns a new instance using the named callable.
	*
	* @param string $name The name of the callable to invoke.
	* @return object A new object instance.
	* @throws UnexpectedValueException
	*/
	public function newInstance($name)
	{
		debug::on(false);
		//if ($name == 'Controller\ResetPassword') { debug::on(); }
		//if (debug::is_on()) { echo '-new-';print_r($name);echo "\n"; }

		$name = ltrim($name, '\\');
		debug::variable($name);

		if (! $this->has($name))
		{
			echo 'ERROR: di->set_closures() not found for "'.$name.'".';
			debug::enabled(true);
			debug::on();
			debug::string('ERROR: di->set_closures not found for "'.$name.'".');
			debug::stack_trace();
			debug::str_exit();
		}
		debug::variable($this->callables[$name], 'this->calleables['.$name.']');
		if (is_callable($this->callables[$name]))
		{
			debug::string('is callable');
			$return = call_user_func($this->callables[$name], $this);
			//if (debug::is_on()) { echo '-new-';print_r($return);echo "\n"; }
		}
		else
		{
			echo "fault<br>"; exit();
		}
		return $return;
	}

	public function delete($name)
	{
		debug::on(false);
		debug::variable($name);
		unset($this->callables[$name]);
		unset($this->instances[$name]);
		unset($this->closures[$name]);
	}
}

?>