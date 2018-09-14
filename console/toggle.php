<?php
//===================================================================
// z9Debug
//===================================================================
// toggle.php
// --------------------
// toggle on/off settings
//
//       Date Created: 2018-03-15
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

define('Z9DEBUG_CONSOLE', true);

define('Z9DEBUG_DIR', dirname(dirname( __FILE__ )));

include(Z9DEBUG_DIR.'/load_console.php');
debug::on(false);

include(Z9DEBUG_DIR.'/settings/config_settings.php');
include(Z9DEBUG_DIR.'/console/functions/console.php');

$is_authenticated = is_valid_auth_token();

if (!$is_authenticated)
{
	exit();
}

$force_enabled = debug::get('force_enabled');
debug::variable($force_enabled);

$toggle_force_enabled = (isset($_POST['force_enabled'])) ? true : false;
debug::variable($toggle_force_enabled);

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

$toggle_force_suppress_output = (isset($_POST['force_suppress_output'])) ? true : false;
debug::variable($toggle_force_suppress_output);

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




$file_path = (isset($_POST['file_path'])) ? $_POST['file_path'] : '';
debug::variable($file_path);

$namespace = (isset($_POST['namespace'])) ? $_POST['namespace'] : '';
debug::variable($namespace);

$class = (isset($_POST['class'])) ? $_POST['class'] : '';
debug::variable($class);

$function = (isset($_POST['function'])) ? $_POST['function'] : '';
debug::variable($function);

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
	if (starts_with($file_path, $_SERVER['DOCUMENT_ROOT']))
	{
		$is_valid_file_path = true;
	}
	debug::variable($is_valid_file_path);

	if (!$is_valid_file_path)
	{
		exit();
	}


	$force_on_path = remove_leading($file_path, $_SERVER['DOCUMENT_ROOT']);
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
	$on_file_function_value = get_on_file_function_value($namespace, $class, $function);
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
			unset($force_on[remove_leading($file_path, $_SERVER['DOCUMENT_ROOT'])]);
		}
		else
		{
			$force_on[remove_leading($file_path, $_SERVER['DOCUMENT_ROOT'])] = $new_on_file_functions;
		}
	}
	else
	{
		// toggle on
		$force_on[remove_leading($file_path, $_SERVER['DOCUMENT_ROOT'])][] = $on_file_function_value;
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

$data_file = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'toggle_settings.php';
debug::variable($data_file);

save_toggle_data($data, $data_file);

if ($toggle_off)
{
	echo '0';
}
else
{
	echo '1';
}

?>