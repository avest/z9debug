<?php
//===================================================================
// z9Debug
//===================================================================
// Toggle.php
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


class Toggle
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
			case 'toggle.php':
				$toggle_force_enabled = (isset($_POST['force_enabled'])) ? true : false;
				debug::variable($toggle_force_enabled);

				$toggle_force_suppress_output = (isset($_POST['force_suppress_output'])) ? true : false;
				debug::variable($toggle_force_suppress_output);

				$file_path = (isset($_POST['file_path'])) ? $_POST['file_path'] : '';
				debug::variable($file_path);

				$namespace = (isset($_POST['namespace'])) ? $_POST['namespace'] : '';
				debug::variable($namespace);

				$class = (isset($_POST['class'])) ? $_POST['class'] : '';
				debug::variable($class);

				$function = (isset($_POST['function'])) ? $_POST['function'] : '';
				debug::variable($function);

				$result = Action::_('Z9\Debug\Console\ToggleSettings')->toggle_on_off(
					$toggle_force_enabled,
					$toggle_force_suppress_output,
					$file_path,
					$namespace,
					$class,
					$function
				);
				debug::variable($result);

				echo $result;

				break;
		}
	}



}



?>