<?php
//===================================================================
// z9Debug
//===================================================================
// Global.php
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


class Globals
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
			case 'global.php':

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

				if (empty($request_id))
				{
					exit();
				}

				return $this->display_global($session_id, $request_id);
				break;
		}
	}


	public function display_global($session_id, $request_id)
	{
		debug::on(false);
		debug::variable($session_id);
		debug::variable($request_id);

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

		$global_data = Action::_('Z9\Debug\Console\GlobalData')->get_global_data($request_dir);
		debug::variable($global_data);


		$this->response->setVars(array(
			'global_data' => $global_data,
		));
		$this->response->setView('global.tpl.php', 'Z9\Debug\Console');

		return $this->response;
	}

}



?>