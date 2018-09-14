<?php
//===================================================================
// z9Debug
//===================================================================
// console.php
// --------------------
// Console controller
//
//       Date Created: 2005-04-23
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

debug::constant(Z9DEBUG_DIR, 'Z9DEBUG_DIR');

$z9debug_dir = str_replace("\\", "/", Z9DEBUG_DIR);
debug::variable($z9debug_dir);

$document_root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
debug::variable($document_root);

$web_root = remove_leading($z9debug_dir, $document_root);
debug::variable($web_root);

$web_root = str_replace("\\", "/", $web_root);
debug::variable($web_root);

if (isset($_POST['password_is_submitted']))
{
	$remote_authentication = debug::get('remote_authentication');
	if (!empty($remote_authentication))
	{
		//--------------------------
		// remote authentication
		//--------------------------
		$username = $_POST['username'];
		$password = $_POST['password'];
		debug::variable($username);
		debug::variable($password);

		$post_url = debug::get('remote_authentication');
		debug::variable($post_url);

		$post_data = array(
			'username' => $username,
			'password' => $password,
		);
		debug::variable($post_data);

		$result = post_to_url($post_data, $post_url);
		debug::variable($result);

		$is_valid_authentication = implode('',$result);
		debug::variable($is_valid_authentication);

		if ($is_valid_authentication == '1')
		{
			set_auth_token($username);

			$redir = (isset($_POST['redir'])) ? $_POST['redir'] : '';
			debug::variable($redir);

			if (!empty($redir))
			{
				header("Location: ".$redir);
				exit();
			}
		}

	}
	else
	{
		//----------------------------------
		// single password authentication
		//----------------------------------
		$password = $_POST['password'];
		debug::variable($password);

		if ($password == debug::get('password'))
		{
			set_auth_token();

			$redir = (isset($_POST['redir'])) ? $_POST['redir'] : '';
			debug::variable($redir);

			if (!empty($redir))
			{
				header("Location: ".$redir);
				exit();
			}
		}
	}
}

$is_authenticated = is_valid_auth_token();
debug::variable($is_authenticated);

if ($is_authenticated && isset($_GET['logout']) && $_GET['logout'] == '1')
{
	// logout
	$cms_cookie_name = 'z9debug_token';
	$_COOKIE[$cms_cookie_name] = '';
	if (debug::get('force_http'))
	{
		setcookie($cms_cookie_name, '', null,"/", null, false);
	}
	else
	{
		setcookie($cms_cookie_name, '', null,"/", null, true);
	}
	debug::string('clearing cookie');
	$is_authenticated = false;
}

if (!$is_authenticated)
{
	$action = 'index.php';
	debug::variable($action);

	$url_parts = parse_url($_SERVER['REQUEST_URI']);
	debug::variable($url_parts);

	$query = '';
	if (isset($url_parts['query']))
	{
		$query = $url_parts['query'];
		if ($query == 'logout=1')
		{
			$query = '';
		}

	}
	debug::variable($query);

	if (!empty($query))
	{
		$action .= '?'.$query;
		debug::variable($action);
	}

	$redir = (isset($_GET['redir'])) ? $_GET['redir'] : '';
	debug::variable($redir);

	include(Z9DEBUG_DIR.'/console/views/login.tpl.php');
	exit();
}


$data_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions';
debug::variable($data_dir);

$session_id = '';
if (isset($_GET['z9dsid']))
{
	$session_id = $_GET['z9dsid'];
}
debug::variable($session_id);

if (empty($session_id))
{
	// find most recent session
	$dir_list = get_dir_dir_list($data_dir);

	if (is_array($dir_list))
	{
		rsort($dir_list);
		debug::variable($dir_list);

		$session_id = '';
		if (isset($dir_list[0]))
		{
			$session_id = $dir_list[0];
		}
		debug::variable($session_id);
	}
}

$session_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$session_id;
debug::variable($session_dir);

if (!is_dir($session_dir))
{
	$session_id = '';
	debug::variable($session_id);
}

$request_id = '';
if (isset($_GET['z9drid']))
{
	$request_id = $_GET['z9drid'];
}
debug::variable($request_id);

$latest_request = false;
if (isset($_GET['latest_request']) && $_GET['latest_request'] ==  '1')
{
	$latest_request = true;
}
debug::variable($latest_request);


if (empty($request_id) || $latest_request)
{
	// find most recent request for session
	$dir_list = get_dir_dir_list($session_dir);
	if (is_array($dir_list))
	{
		rsort($dir_list);
		debug::variable($dir_list);

		$request_id = '';
		if (isset($dir_list[0]))
		{
			$request_id = $dir_list[0];
		}
		debug::variable($request_id);
	}
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
	$request_id = '';
	debug::variable($request_id);
}
if (!is_dir($request_dir))
{
	$request_id = '';
	debug::variable($request_id);
}

$page_data = '';
$page_peak_memory = '';
$request_full_url = '';
$request_date = '';
$request_url_path = '';
$request_url_query = '';
$page_load_time = '';
$page_sql_time = '';
$var_data_page_count = '';
$var_data_page = '';

if (!empty($request_id))
{
	$page_data = get_page_data($request_dir);
	debug::variable($page_data);

	$page_peak_memory = $page_data['page_peak_memory'];
	debug::variable($page_peak_memory);

	$request_full_url = $page_data['request_full_url'];
	debug::variable($request_full_url);

	$request_date = $page_data['request_date'];
	debug::variable($request_date);

	$request_url_path = $page_data['request_url_path'];
	debug::variable($request_url_path);

	$request_url_query = $page_data['request_url_query'];
	debug::variable($request_url_query);

	$page_load_time = $page_data['page_load_time'];
	debug::variable($page_load_time);

	$page_sql_time = $page_data['page_sql_time'];
	if (empty($page_sql_time))
	{
		$page_sql_time = '0.0000';
	}
	debug::variable($page_sql_time);

	$var_data_page_count = $page_data['var_data_page_count'];
	debug::variable($var_data_page_count);

	$var_data_page = 1;
	if (isset($_GET['page']))
	{
		if (is_numeric($_GET['page']))
		{
			if ($_GET['page'] <= $var_data_page_count)
			{
				if ($_GET['page'] > 0)
				{
					$var_data_page = $_GET['page'];
				}
			}
		}
	}
	debug::variable($var_data_page);
}

$request_data = get_request_data($session_dir);
debug::variable($request_data);

purge_old_requests($request_data, $session_dir);

update_session_last_mod_time($session_dir);

purge_old_sessions($session_id, $data_dir);

// check if cms is installed
$is_cms_installed = false;
if (debug::get('is_cms_installed'))
{
	$is_cms_installed = true;
}
debug::variable($is_cms_installed);

// set default for toggle on/off page
$physical_dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR;
debug::variable($physical_dir);

include(Z9DEBUG_DIR.'/console/views/console.tpl.php');

?>