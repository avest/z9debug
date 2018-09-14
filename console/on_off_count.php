<?php
//===================================================================
// z9Debug
//===================================================================
// on_off_count.php
// --------------------
// process ajax request for on off count.
//
//       Date Created: 2018-09-14
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

if (is_file(Z9DEBUG_DIR.'/settings/toggle_settings.php'))
{
	include(Z9DEBUG_DIR.'/settings/toggle_settings.php');
}
$on_off_count = 0;

$force_on = debug::get('force_on');
debug::variable($force_on);

if (is_array($force_on))
{
	foreach ($force_on as $file_name => $file_name_settings)
	{
		if (is_array($file_name_settings))
		{
			foreach ($file_name_settings as $file_index => $file_setting)
			{
				$on_off_count++;
			}
		}
	}
}

include(Z9DEBUG_DIR.'/console/views/on_off_count.tpl.php');