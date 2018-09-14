<?php
//===================================================================
// z9Debug
//===================================================================
// toggle_breadcrumb.php
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
// get breadcrumb...
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

$breadcrumb_dir = remove_leading($physical_path, $_SERVER['DOCUMENT_ROOT']);
debug::variable($breadcrumb_dir);

$breadcrumb = make_breadcrumb($breadcrumb_dir);
debug::variable($breadcrumb);

include(Z9DEBUG_DIR.'/console/views/toggle_breadcrumb.tpl.php');

?>