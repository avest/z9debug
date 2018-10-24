<?php
//===================================================================
// z9Debug
//===================================================================
// Sql.php
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
use Facade\Php;
use Facade\File;


class Sql
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

		$is_authenticated = $this->authenticate->is_valid_auth_token();
		debug::variable($is_authenticated);

		if (!$is_authenticated)
		{
			exit();
		}

		$url_parts = parse_url($this->request->_SERVER['REQUEST_URI']);
		debug::variable($url_parts, 'url_parts');

		$base_name = basename($url_parts['path']);
		debug::variable($base_name, 'base_name');

		switch ($base_name)
		{
			case 'sql.php':

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

				$request_id = '';
				if (isset($_POST['request_id']))
				{
					$request_id = $_POST['request_id'];
				}
				debug::variable($request_id);

				$slow_queries = (isset($_POST['slow_queries']) && $_POST['slow_queries'] == '1') ? true : false;
				debug::variable($slow_queries);

				return $this->display_sql($session_id, $request_id, $slow_queries);
				break;
		}
	}

	public function display_sql($session_id, $request_id, $slow_queries)
	{
		debug::on(false);
		debug::variable($session_id);
		debug::variable($request_id);
		debug::variable($slow_queries);

		$web_root = Config::get('path.debug.web_root');
		debug::variable($web_root);

		$data_dir = Config::get('path.debug.data_dir');
		debug::variable($data_dir);

		$session_dir = $data_dir.DIRECTORY_SEPARATOR.$session_id;
		debug::variable($session_dir);

		$request_dir = $session_dir.DIRECTORY_SEPARATOR.$request_id;
		debug::variable($request_dir);

		//if (DIRECTORY_SEPARATOR == '/')
		//{
		//	// linux
		//	$request_dir = str_replace("\\", "/", $request_dir);
		//}
		//elseif (DIRECTORY_SEPARATOR == '\\')
		//{
		//	// windows
		//	$request_dir = str_replace("/", "\\", $request_dir);
		//}
		//debug::variable($request_dir);

		$confirm_request_dir = realpath($request_dir);
		debug::variable($confirm_request_dir);

		if ($request_dir <> $confirm_request_dir)
		{
			exit();
		}

		$sql_data = Action::_('Z9\Debug\Console\SqlData')->get_sql_data($request_dir);
		debug::variable($sql_data);

		$this->response->setVars(array(
			'sql_data' => $sql_data,
			'slow_queries' => $slow_queries,
			'session_id' => $session_id,
			'request_id' => $request_id,
		));
		$this->response->setView('sql.tpl.php', 'Z9\Debug\Console');

		return $this->response;

	}

}



?>