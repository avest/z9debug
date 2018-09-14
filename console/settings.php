<?php
//===================================================================
// z9Debug
//===================================================================
// settings.php
// --------------------
//
//       Date Created: 2018-03-17
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
debug::variable($is_authenticated);

if (!$is_authenticated)
{
	exit();
}

include(Z9DEBUG_DIR.'/console/views/settings.tpl.php');

?>