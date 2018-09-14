<?php
//===================================================================
// z9Debug
//===================================================================
// toggle_function_list.php
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

$functions = array();

// get list of functions...
$physical_path = $_SERVER['DOCUMENT_ROOT'];
if (isset($_POST['physical_path']))
{
	$physical_path = $_POST['physical_path'];
}
debug::variable($physical_path);

if (ends_with($physical_path, DIRECTORY_SEPARATOR))
{
	$confirm_physical_path = realpath($physical_path).DIRECTORY_SEPARATOR;
}
else
{
	$confirm_physical_path = realpath($physical_path);
}
debug::variable($confirm_physical_path);

if ($physical_path <> $confirm_physical_path)
{
	exit();
}

$is_valid_physical_path = false;
if (starts_with($physical_path, $_SERVER['DOCUMENT_ROOT']))
{
	$is_valid_physical_path = true;
}
debug::variable($is_valid_physical_path);

if (!$is_valid_physical_path)
{
	exit();
}

$display_file_path = remove_leading($physical_path, $_SERVER['DOCUMENT_ROOT']);
debug::variable($display_file_path);

if (!empty($physical_path))
{
	$functions = parse_php_file($physical_path);
	debug::variable($functions);
}

$is_physical_file = false;
if (!ends_with($physical_path, DIRECTORY_SEPARATOR))
{
	$is_physical_file = true;
}
debug::variable($is_physical_file);

if (empty($functions) && $is_physical_file)
{
	$functions[] = array(
		'file_path' => $physical_path,
		'namespace' => '',
		'class' => '',
		'function' => '',
		'line_number' => '',
	);
	debug::variable($functions);
}


// get list of files already set to on
$force_on = debug::get('force_on');
debug::variable($force_on);

$force_on_path = remove_leading($physical_path, $_SERVER['DOCUMENT_ROOT']);
debug::variable($force_on_path);

// we only need the settings for the one file
$on_file_functions = array();
if (isset($force_on[$force_on_path]))
{
	$on_file_functions = $force_on[$force_on_path];
}
debug::variable($on_file_functions);

if (is_array($functions))
{
	foreach ($functions as $key => $function)
	{
		debug::variable($function);

		// Channel/Bigcommerce/Controller/Index::__invoke
		// namespace = Channel\Bigcommerce\Controller
		// class = Index
		$on_file_function_value = get_on_file_function_value($function['namespace'], $function['class'], $function['function']);
		debug::variable($on_file_function_value);

		$is_on = false;
		if (in_array($on_file_function_value, $on_file_functions))
		{
			$is_on = true;
		}
		debug::variable($is_on);

		$functions[$key]['is_on'] = $is_on;
	}
}
debug::variable($functions);

include(Z9DEBUG_DIR.'/console/views/toggle_function_list.tpl.php');

?>