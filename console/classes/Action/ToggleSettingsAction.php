<?php
//===================================================================
// z9Debug
//===================================================================
// ToggleAction.php
// --------------------
//
//       Date Created: 2018-10-22
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================


namespace Z9\Debug\Console\Action;

use debug;
use Facade\File;
use Facade\Str;
use Facade\Config;

class ToggleSettingsAction
{
	public function is_toggled_on($file_path, $namespace, $class, $function)
	{
		debug::on(false);
		debug::variable($file_path);
		debug::variable($namespace);
		debug::variable($class);
		debug::variable($function);

		$force_on = debug::get('force_on');
		debug::variable($force_on);

		$force_on_path = Str::remove_leading($file_path, $_SERVER['DOCUMENT_ROOT']);
		debug::variable($force_on_path);

		// we need the current on_file_function settings
		$on_file_functions = array();
		if (isset($force_on[$force_on_path]))
		{
			$on_file_functions = $force_on[$force_on_path];
		}
		debug::variable($on_file_functions);

		// are we toggling on or off?

		// Channel/Bigcommerce/Controller/Index::__invoke
		// namespace = Channel\Bigcommerce\Controller
		// class = Index
		$on_file_function_value = $this->get_on_file_function_value($namespace, $class, $function);
		debug::variable($on_file_function_value);

		$is_on = false;
		if (in_array($on_file_function_value, $on_file_functions))
		{
			$is_on = true;
		}
		debug::variable($is_on);

		return $is_on;
	}

	public function get_on_file_function_value($namespace, $class, $function)
	{
		debug::on(false);
		debug::variable($namespace);
		debug::variable($class);
		debug::variable($function);

		$on_file_function_value = '';
		if (!empty($namespace))
		{
			$on_file_function_value .= $namespace;
		}
		if (!empty($namespace) && !empty($class))
		{
			$on_file_function_value .= '\\';
		}
		if (!empty($class))
		{
			$on_file_function_value .= $class;
		}
		if (!empty($class))
		{
			$on_file_function_value .= '::';
		}
		if (!empty($function))
		{
			$on_file_function_value .= $function;
		}
		if (empty($on_file_function_value))
		{
			$on_file_function_value = '-';
		}
		$on_file_function_value = str_replace('\\', '/', $on_file_function_value);
		debug::variable($on_file_function_value);

		return $on_file_function_value;
	}

	public function toggle_on_off(
		$toggle_force_enabled,
		$toggle_force_suppress_output,
		$file_path,
		$namespace,
		$class,
		$function
		)
	{
		debug::on(false);
		debug::variable($toggle_force_enabled);
		debug::variable($toggle_force_suppress_output);
		debug::variable($file_path);
		debug::variable($namespace);
		debug::variable($class);
		debug::variable($function);

		$force_enabled = debug::get('force_enabled');
		debug::variable($force_enabled);

		if ($toggle_force_enabled)
		{
			if ($force_enabled)
			{
				$force_enabled = false;
				debug::variable($force_enabled);
			}
			else
			{
				$force_enabled = true;
				debug::variable($force_enabled);
			}
		}

		$force_suppress_output = debug::get('force_suppress_output');
		debug::variable($force_suppress_output);

		if ($toggle_force_suppress_output)
		{
			if ($force_suppress_output)
			{
				$force_suppress_output = false;
				debug::variable($force_suppress_output);
			}
			else
			{
				$force_suppress_output = true;
				debug::variable($force_suppress_output);
			}
		}

		// get list of files already set to on
		$force_on = debug::get('force_on');
		debug::variable($force_on);

		if (!empty($file_path))
		{
			$confirm_file_path = realpath($file_path);
			debug::variable($confirm_file_path);

			if ($file_path <> $confirm_file_path)
			{
				exit();
			}

			$is_valid_file_path = false;
			if (Str::starts_with($file_path, $_SERVER['DOCUMENT_ROOT']))
			{
				$is_valid_file_path = true;
			}
			debug::variable($is_valid_file_path);

			if (!$is_valid_file_path)
			{
				exit();
			}


			$force_on_path = Str::remove_leading($file_path, $_SERVER['DOCUMENT_ROOT']);
			debug::variable($force_on_path);

			// we need the current on_file_function settings
			$on_file_functions = array();
			if (isset($force_on[$force_on_path]))
			{
				$on_file_functions = $force_on[$force_on_path];
			}
			debug::variable($on_file_functions);

			// are we toggling on or off?

			// Channel/Bigcommerce/Controller/Index::__invoke
			// namespace = Channel\Bigcommerce\Controller
			// class = Index
			$on_file_function_value = $this->get_on_file_function_value($namespace, $class, $function);
			debug::variable($on_file_function_value);

			$is_on = false;
			if (in_array($on_file_function_value, $on_file_functions))
			{
				$is_on = true;
			}
			debug::variable($is_on);

			$toggle_off = false;
			if ($is_on)
			{
				$toggle_off = true;
			}
			debug::variable($toggle_off);

			if ($is_on)
			{
				// toggle off
				$new_on_file_functions = array();
				if (is_array($on_file_functions))
				{
					foreach ($on_file_functions as $key => $on_file_function)
					{
						if ($on_file_function <> $on_file_function_value)
						{
							$new_on_file_functions[] = $on_file_function;
						}
					}
				}
				debug::variable($new_on_file_functions);

				if (empty($new_on_file_functions))
				{
					debug::string('new_on_file_functions is empty');
					unset($force_on[Str::remove_leading($file_path, $_SERVER['DOCUMENT_ROOT'])]);
				}
				else
				{
					$force_on[Str::remove_leading($file_path, $_SERVER['DOCUMENT_ROOT'])] = $new_on_file_functions;
				}
			}
			else
			{
				// toggle on
				$force_on[Str::remove_leading($file_path, $_SERVER['DOCUMENT_ROOT'])][] = $on_file_function_value;
			}
			debug::variable($force_on);
		}


		//---------------------------------------------------------------
		// update the force_on settings in the toggle_settings.php file.
		//---------------------------------------------------------------
		$data = array(
			'force_enabled' => $force_enabled,
			'force_suppress_output' => $force_suppress_output,
			'force_on' => $force_on,
		);
		debug::variable($data);

		$settings_dir = Config::get('path.debug.settings_dir');
		debug::variable($settings_dir);

		$data_file = $settings_dir.DIRECTORY_SEPARATOR.'toggle_settings.php';
		debug::variable($data_file);

		$this->save_toggle_data($data, $data_file);

		if ($toggle_off)
		{
			return '0';
		}
		else
		{
			return '1';
		}
	}

	public function save_toggle_data($data, $data_file)
	{
		debug::on(false);
		debug::variable($data);
		debug::variable($data_file);

		//debug::set('force_enabled', true);
		//debug::set('force_suppress_output', true);
		//debug::set('force_on', array(
		//	'/classes/Channel/Bigcommerce/Controller/Index.php' => array(
		//		'Channel/Bigcommerce/Controller/Index::__invoke',
		//	),
		//));

		$data_content = "";
		$data_content .= "<"."?php\r\n";

		if (isset($data['force_enabled']) && $data['force_enabled'])
		{
			$data_content .= "debug::set('force_enabled', true);\r\n";
		}
		else
		{
			$data_content .= "debug::set('force_enabled', false);\r\n";
		}

		if (isset($data['force_suppress_output']) && $data['force_suppress_output'])
		{
			$data_content .= "debug::set('force_suppress_output', true);\r\n";
		}
		else
		{
			$data_content .= "debug::set('force_suppress_output', false);\r\n";
		}

		$data_content .= "debug::set('force_on', ";
		$data_content .= $this->convert_var_value_to_string($data['force_on']);
		$data_content .= ");\r\n";

		$data_content .= "?".">\r\n";
		debug::variable($data_content);


		// if file exist, let's rename it to a backup copy

		$data_file_name_base = basename($data_file);
		debug::variable($data_file_name_base);

		$backup_data_file_name_base = "bak.".$data_file_name_base;
		debug::variable($backup_data_file_name_base);

		$backup_data_file = dirname($data_file).DIRECTORY_SEPARATOR.$backup_data_file_name_base;
		debug::variable($backup_data_file);

		if (file_exists($backup_data_file))
		{
			chmod($backup_data_file, 0777);
			unlink($backup_data_file);
		}
		if (file_exists($data_file))
		{
			rename($data_file, $backup_data_file);
		}

		// write file
		$fd = fopen($data_file, "w");
		fwrite($fd, $data_content);
		fclose($fd);
	}

	private function convert_var_value_to_string($var_value)
	{
		debug::on(false);
		debug::variable($var_value);

		$return = '';
		$return .= stripslashes(var_export($var_value, true));
		$return .= "\r\n";
		return $return;
	}


}

?>