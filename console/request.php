<?php
//===================================================================
// z9Debug
//===================================================================
// request.php
// --------------------
// request ajax call
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

$is_delete = false;
if (isset($_POST['delete']) && $_POST['delete'] == '1')
{
	$is_delete = true;
}
debug::variable($is_delete);

if ($is_delete)
{
	$request_id = (isset($_POST['request_id'])) ? $_POST['request_id'] : '';
	debug::variable($request_id);

	if ($request_id == 'ALL')
	{
		$requests = get_request_data($session_dir);
		debug::variable($requests);

		if (is_array($requests))
		{
			foreach ($requests as $request)
			{
				$request_id = $request['request_id'];
				debug::variable($request_id);

				$dir_path = $session_dir.DIRECTORY_SEPARATOR.$request_id;
				debug::variable($dir_path);

				delete_dir($dir_path);
			}
		}
	}
	elseif (!empty($request_id))
	{
		$dir_path = $session_dir.DIRECTORY_SEPARATOR.$request_id;
		debug::variable($dir_path);

		delete_dir($dir_path);
	}
}

$request_id = '';

$request_data = get_request_data($session_dir);
debug::variable($request_data);

include(Z9DEBUG_DIR.'/console/views/request.tpl.php');

?>