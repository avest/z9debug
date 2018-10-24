<?php
//===================================================================
// z9Debug
//===================================================================
// ToggleOnList.php
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
use Facade\Date;

class ToggleOnList
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
			case 'toggle_on_list.php':

				return $this->display_toggle_on_list();

				break;
		}
	}

	public function display_toggle_on_list()
	{
		debug::on(false);

		// get list of files already set to on
		$force_on = debug::get('force_on');
		debug::variable($force_on);

		$this->response->setVars(array(
			'force_on' => $force_on,
		));
		$this->response->setView('toggle_on_list.tpl.php', 'Z9\Debug\Console');

		return $this->response;

	}

}



?>