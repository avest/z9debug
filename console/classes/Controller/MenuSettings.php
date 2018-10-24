<?php
//===================================================================
// z9Debug
//===================================================================
// MenuSettings.php
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


class MenuSettings
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
			case 'menu_settings.php':

				return $this->display_menu_settings();
				break;
		}
	}


	public function display_menu_settings()
	{
		debug::on(false);

		$force_enabled = debug::get('force_enabled');
		debug::variable($force_enabled);

		$force_suppress_output = debug::get('force_suppress_output');
		debug::variable($force_suppress_output);

		$settings_dir = Config::get('path.debug.settings_dir');
		debug::variable($settings_dir);

		$dir_exists = false;
		if (is_dir($settings_dir))
		{
			$dir_exists = true;
		}
		debug::variable($dir_exists);

		$is_writeable_dir = false;
		if (is_writeable($settings_dir))
		{
			$is_writeable_dir = true;
		}
		debug::variable($is_writeable_dir);

		if (!$is_writeable_dir)
		{
			@chmod($settings_dir, 0777);
			$is_writeable_dir = false;
			if (is_writeable($settings_dir))
			{
				$is_writeable_dir = true;
			}
			debug::variable($is_writeable_dir);
		}

		$settings_file = $settings_dir.DIRECTORY_SEPARATOR.'toggle_settings.php';
		debug::variable($settings_file);

		$file_exists = false;
		if (is_file($settings_file))
		{
			$file_exists = true;
		}
		debug::variable($file_exists);

		$is_writeable_file = false;
		if (is_writeable($settings_file))
		{
			$is_writeable_file = true;
		}
		debug::variable($is_writeable_file);

		if (!$is_writeable_file)
		{
			@chmod($settings_file, 0777);
			$is_writeable_file = false;
			if (is_writeable($settings_file))
			{
				$is_writeable_file = true;
			}
			debug::variable($is_writeable_file);
		}



		$this->response->setVars(array(
			'force_enabled' => $force_enabled,
			'force_suppress_output' => $force_suppress_output,
		));
		$this->response->setView('menu_settings.tpl.php', 'Z9\Debug\Console');

		return $this->response;
	}

}



?>