<?php
//===================================================================
// z9Debug
//===================================================================
// Request.php
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


class Requests
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
			case 'request.php':

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

				$is_delete = false;
				if (isset($_POST['delete']) && $_POST['delete'] == '1')
				{
					$is_delete = true;
				}
				debug::variable($is_delete);

				if ($is_delete)
				{
					Action::_('Z9\Debug\Console\RequestData')->delete_requests($session_id, $request_id);
				}

				return $this->display_request($session_id, $request_id);
				break;
		}
	}

	public function display_request($session_id, $request_id)
	{
		debug::on(false);
		debug::variable($session_id);
		debug::variable($request_id);

		$data_dir = Config::get('path.debug.data_dir');
		debug::variable($data_dir);

		$session_dir = $data_dir.DIRECTORY_SEPARATOR.$session_id;
		debug::variable($session_dir);

		//$request_id = '';

		$request_data = Action::_('Z9\Debug\Console\RequestData')->get_request_data($session_dir);
		debug::variable($request_data);

		$this->response->setVars(array(
			'request_data' => $request_data,
			'session_id' => $session_id,
			'request_id' => $request_id,
		));
		$this->response->setView('request.tpl.php', 'Z9\Debug\Console');

		return $this->response;

	}

}



?>