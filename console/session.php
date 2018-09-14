<?php
//===================================================================
// z9Debug
//===================================================================
// session.php
// --------------------
// session ajax call
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

$is_delete = false;
if (isset($_POST['delete']) && $_POST['delete'] == '1')
{
	$is_delete = true;
}
debug::variable($is_delete);

if ($is_delete)
{
	$session_id = (isset($_POST['session_id'])) ? $_POST['session_id'] : '';
	debug::variable($session_id);

	if ($session_id == 'ALL')
	{
		$sessions = get_session_data($data_dir);
		debug::variable($sessions);

		if (is_array($sessions))
		{
			foreach ($sessions as $session)
			{
				$session_id = $session['session_id'];
				debug::variable($session_id);

				$dir_path = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$session_id;
				debug::variable($dir_path);

				debug::string('delete_dir');
				delete_dir($dir_path);
			}
		}
	}
	elseif (!empty($session_id))
	{
		$dir_path = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$session_id;
		debug::variable($dir_path);

		delete_dir($dir_path);
	}
}

$session_id = '';
debug::variable($session_id);

$session_data = get_session_data($data_dir);
debug::variable($session_data);

include(Z9DEBUG_DIR.'/console/views/session.tpl.php');

?>