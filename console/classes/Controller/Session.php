<?php
//===================================================================
// z9Debug
//===================================================================
// Session.php
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


class Session
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
			case 'session.php':

				$is_delete = false;
				if (isset($_POST['delete']) && $_POST['delete'] == '1')
				{
					$is_delete = true;
				}
				debug::variable($is_delete);

				$session_id = (isset($_POST['session_id'])) ? $_POST['session_id'] : '';
				debug::variable($session_id);

				if ($is_delete)
				{
					$data_dir = Config::get('path.debug.data_dir');
					debug::variable($data_dir);

					Action::_('Z9\Debug\Console\SessionData')->delete_sessions($data_dir, $session_id);
				}

				return $this->display_sessions($session_id);
				break;
		}
	}


	public function display_sessions($session_id)
	{
		debug::on(false);
		debug::variable($session_id);

		$data_dir = Config::get('path.debug.data_dir');
		debug::variable($data_dir);

		$session_data = Action::_('Z9\Debug\Console\SessionData')->get_session_data($data_dir);
		debug::variable($session_data);

		$this->response->setVars(array(
			'session_id' => $session_id,
			'session_data' => $session_data,
		));
		$this->response->setView('session.tpl.php', 'Z9\Debug\Console');

		return $this->response;
	}

}



?>