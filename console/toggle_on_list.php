<?php
//===================================================================
// z9Debug
//===================================================================
// toggle_on_list.php
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

// get list of files already set to on
$force_on = debug::get('force_on');
debug::variable($force_on);

include(Z9DEBUG_DIR.'/console/views/toggle_on_list.tpl.php');

?>