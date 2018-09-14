<?php
//===================================================================
// z9Debug
//===================================================================
// sql.php
// --------------------
// sql ajax call
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

$web_root = remove_leading(str_replace("\\", "/", Z9DEBUG_DIR), str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']));

$web_root = str_replace("\\", "/", $web_root);
debug::variable($web_root);


$is_authenticated = is_valid_auth_token();
debug::variable($is_authenticated);

if (!$is_authenticated)
{
	exit();
}

$data_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions';
debug::variable($data_dir);

$session_id = '';
if (isset($_POST['session_id']))
{
	$session_id = $_POST['session_id'];
}
debug::variable($session_id);

if (empty($session_id))
{
	exit();
}

$session_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$session_id;
debug::variable($session_dir);

$request_id = '';
if (isset($_POST['request_id']))
{
	$request_id = $_POST['request_id'];
}
debug::variable($request_id);

if (empty($request_id))
{
	exit();
}

$request_dir = Z9DEBUG_DIR.'/sessions/'.$session_id.'/'.$request_id;
debug::variable($request_dir);

if (DIRECTORY_SEPARATOR == '/')
{
	// linux
	$request_dir = str_replace("\\", "/", $request_dir);
}
elseif (DIRECTORY_SEPARATOR == '\\')
{
	// windows
	$request_dir = str_replace("/", "\\", $request_dir);
}
debug::variable($request_dir);

$confirm_request_dir = realpath($request_dir);
debug::variable($confirm_request_dir);

if ($request_dir <> $confirm_request_dir)
{
	exit();
}

$sql_data = get_sql_data($request_dir);
debug::variable($sql_data);

include(Z9DEBUG_DIR.'/console/views/sql.tpl.php');

?>