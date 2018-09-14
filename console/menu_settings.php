<?php
//===================================================================
// z9Debug
//===================================================================
// menu_settings.php
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

$force_suppress_output = debug::get('force_suppress_output');
debug::variable($force_suppress_output);

$data_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR;
debug::variable($data_dir);

$dir_exists = false;
if (is_dir($data_dir))
{
	$dir_exists = true;
}
debug::variable($dir_exists);

$is_writeable_dir = false;
if (is_writeable($data_dir))
{
	$is_writeable_dir = true;
}
debug::variable($is_writeable_dir);

if (!$is_writeable_dir)
{
	@chmod($data_dir, 0777);
	$is_writeable_dir = false;
	if (is_writeable($data_dir))
	{
		$is_writeable_dir = true;
	}
	debug::variable($is_writeable_dir);
}

$data_file = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'toggle_settings.php';
debug::variable($data_file);

$file_exists = false;
if (is_file($data_file))
{
	$file_exists = true;
}
debug::variable($file_exists);

$is_writeable_file = false;
if (is_writeable($data_file))
{
	$is_writeable_file = true;
}
debug::variable($is_writeable_file);

if (!$is_writeable_file)
{
	@chmod($data_file, 0777);
	$is_writeable_file = false;
	if (is_writeable($data_file))
	{
		$is_writeable_file = true;
	}
	debug::variable($is_writeable_file);
}


include(Z9DEBUG_DIR.'/console/views/menu_settings.tpl.php');

?>