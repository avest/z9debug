<?php
//===================================================================
// z9Debug
//===================================================================
// error.php
// --------------------
// error ajax call
//
//       Date Created: 2018-01-14
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

$message = (isset($_POST['message'])) ? $_POST['message'] : '';
debug::variable($message);

include(Z9DEBUG_DIR.'/console/views/error.tpl.php');

?>