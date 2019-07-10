<?php
//===================================================================
// z9Debug
//===================================================================
// Index.php
// --------------------
//
//       Date Created: 2018-10-22
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

namespace Z9\Debug\Console\Controller;

use debug;
use Facade\Dependency;
use Mlaphp\Request;
use Mlaphp\Response;
use Facade\Str;
use Z9\Debug\Console\Authenticate;
use Facade\Action;
use Facade\Config;


class Index
{
	/** @var Request */
	protected $request;
	/** @var Response */
	protected $response;
	/** @var \Z9\Debug\Console\Authenticate */
	protected $authenticate;

	public function __construct(
	)
	{
		$this->request = Dependency::inject('Request');
		$this->response = Dependency::inject('Response');
		$this->authenticate = Dependency::inject('\Z9\Debug\Console\Authenticate');
	}

	public function __invoke()
	{
		debug::on(false);
		debug::string('__invoke');

		// CAPTURE INPUTS
		$password_is_submitted = (isset($_POST['password_is_submitted'])) ? $_POST['password_is_submitted'] : '';
		debug::variable($password_is_submitted);

		$redir = (isset($_POST['redir'])) ? $_POST['redir'] : '';
		debug::variable($redir);

		if (empty($redir))
		{
			$redir = (isset($_GET['redir'])) ? $_GET['redir'] : '';
			debug::variable($redir);
		}

		$is_logout = (isset($_GET['logout']) && $_GET['logout'] == '1') ? true : false;
		debug::variable($is_logout);

		$session_id = '';
		if (isset($_GET['z9dsid']))
		{
			$session_id = $_GET['z9dsid'];
		}
		debug::variable($session_id);

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


		$is_authenticated = $this->authenticate->is_valid_auth_token();
		debug::variable($is_authenticated);
		//echo "is_authenticated=<pre>".print_r($is_authenticated, true)."</pre><br>";

		// LOGIN
		if (!empty($password_is_submitted))
		{
			$username = (isset($_POST['username'])) ? $_POST['username'] : '';
			debug::variable($username);

			$password = (isset($_POST['password'])) ? $_POST['password'] : '';
			debug::variable($password);

			$is_valid_login = Action::_('Z9\Debug\Console\User')->login($username, $password);
			debug::variable($is_valid_login);
			//echo "is_valid_login=<pre>".print_r($is_valid_login, true)."</pre><br>";

			if ($is_valid_login)
			{
				$is_authenticated = true;
				debug::variable($is_authenticated);

				if (!empty($redir))
				{
					header("Location: ".$redir);
					exit();
				}
			}

		}

		// LOGOUT
		if ($is_authenticated && $is_logout)
		{
			Action::_('Z9\Debug\Console\User')->logout();
			$is_authenticated = false;
		}

		// NOT AUTHENTICATED
		if (!$is_authenticated)
		{
			return $this->display_login($redir);
		}


		// CHECK FOR WRITEABLE SESSIONS FOLDER
		$sessions_dir = Config::get('path.debug.sessions_dir');
		debug::variable($sessions_dir);

		if (!is_writeable($sessions_dir))
		{
			echo "sessions directory is not writeable.<br>";
			exit();
		}


		$url_parts = parse_url($this->request->_SERVER['REQUEST_URI']);
		debug::variable($url_parts, 'url_parts');

		$base_name = basename($url_parts['path']);
		debug::variable($base_name, 'base_name');

		switch ($base_name)
		{
			case 'console':
			case 'index.php':
				return $this->display_index($session_id, $request_id, $latest_request);
				break;
		}
	}


	public function display_login($redir)
	{
		debug::on(false);
		debug::variable($redir);

		$web_root = Config::get('path.debug.web_root');
		debug::variable($web_root);

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

		$this->response->setVars(array(
			'web_root' => $web_root,
			'redir' => $redir,
			'action' => $action,
		));
		$this->response->setView('login.tpl.php', 'Z9\Debug\Console');

		return $this->response;
	}

	public function display_index($session_id, $request_id, $latest_request)
	{
		debug::on(false);
		debug::variable($session_id);
		debug::variable($request_id);
		debug::variable($latest_request);

		$z9debug_dir = Config::get('path.debug.z9debug_dir');
		debug::variable($z9debug_dir);

		$document_root = Config::get('path.debug.document_root');
		debug::variable($document_root);

		$web_root = Config::get('path.debug.web_root');
		debug::variable($web_root);

		$sessions_dir = Config::get('path.debug.sessions_dir');
		debug::variable($sessions_dir);

		$data_dir = Config::get('path.debug.data_dir');
		debug::variable($data_dir);

		// set default for toggle on/off page
		$physical_dir = Config::get('path.debug.physical_dir');
		debug::variable($physical_dir);

		if (empty($session_id))
		{
			// find most recent session
			$session_id = Action::_('Z9\Debug\Console\SessionData')->get_most_recent_session_id($data_dir);
			debug::variable($session_id);
		}

		$session_dir = $sessions_dir.DIRECTORY_SEPARATOR.$session_id;
		debug::variable($session_dir);

		if (!is_dir($session_dir))
		{
			$session_id = '';
			debug::variable($session_id);
		}

		if (empty($request_id) || $latest_request)
		{
			// find most recent request for session
			$request_id = Action::_('Z9\Debug\Console\RequestData')->get_most_recent_request_id($session_dir);
			debug::variable($request_id);
		}

		$request_dir = $session_dir.DIRECTORY_SEPARATOR.$request_id;
		debug::variable($request_dir);

//		if (DIRECTORY_SEPARATOR == '/')
//		{
//			// linux
//			$request_dir = str_replace("\\", "/", $request_dir);
//		}
//		elseif (DIRECTORY_SEPARATOR == '\\')
//		{
//			// windows
//			$request_dir = str_replace("/", "\\", $request_dir);
//		}
//		debug::variable($request_dir);

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
			$page_data = Action::_('Z9\Debug\Console\PageData')->get_page_data($request_dir);
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

		$request_data = Action::_('Z9\Debug\Console\RequestData')->get_request_data($session_dir);
		debug::variable($request_data);

		Action::_('Z9\Debug\Console\RequestData')->purge_old_requests($request_data, $session_dir);

		Action::_('Z9\Debug\Console\SessionData')->update_session_last_mod_time($session_dir);

		Action::_('Z9\Debug\Console\SessionData')->purge_old_sessions($session_id, $data_dir);

		// check if cms is installed
		$is_cms_installed = false;
		if (debug::get('is_cms_installed'))
		{
			$is_cms_installed = true;
		}
		debug::variable($is_cms_installed);



		$this->response->setVars(array(
			'web_root' => $web_root,
			'session_id' => $session_id,
			'request_id' => $request_id,
			'request_date' => $request_date,
			'request_url_path' => $request_url_path,
			'var_data_page_count' => $var_data_page_count,
			'is_cms_installed' => $is_cms_installed,
			'page_load_time' => $page_load_time,
			'page_peak_memory' => $page_peak_memory,
			'page_sql_time' => $page_sql_time,
			'physical_dir' => $physical_dir,
		));
		$this->response->setView('console.tpl.php', 'Z9\Debug\Console');

		return $this->response;
	}

}



?>