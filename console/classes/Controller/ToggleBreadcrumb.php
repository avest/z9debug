<?php
//===================================================================
// z9Debug
//===================================================================
// ToggleBreadcrumb.php
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


class ToggleBreadcrumb
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
			case 'toggle_breadcrumb.php':
				$physical_path = $_SERVER['DOCUMENT_ROOT'];
				if (isset($_POST['physical_path']))
				{
					$physical_path = $_POST['physical_path'];
				}
				debug::variable($physical_path);

				return $this->display_toggle_breadcrumb($physical_path);

				break;
		}
	}

	public function display_toggle_breadcrumb($physical_path)
	{
		debug::on(false);
		debug::variable($physical_path);


		if (Str::ends_with($physical_path, DIRECTORY_SEPARATOR))
		{
			$confirm_physical_path = realpath($physical_path).DIRECTORY_SEPARATOR;
		}
		else
		{
			$confirm_physical_path = realpath($physical_path);
		}
		debug::variable($confirm_physical_path);

		if ($physical_path <> $confirm_physical_path)
		{
			exit();
		}

		$is_valid_physical_path = false;
		if (Str::starts_with($physical_path, $_SERVER['DOCUMENT_ROOT']))
		{
			$is_valid_physical_path = true;
		}
		debug::variable($is_valid_physical_path);

		if (!$is_valid_physical_path)
		{
			exit();
		}

		$breadcrumb_dir = Str::remove_leading($physical_path, $_SERVER['DOCUMENT_ROOT']);
		debug::variable($breadcrumb_dir);

		$breadcrumb = $this->make_breadcrumb($breadcrumb_dir);
		debug::variable($breadcrumb);

		$this->response->setVars(array(
			'breadcrumb' => $breadcrumb,
		));
		$this->response->setView('toggle_breadcrumb.tpl.php', 'Z9\Debug\Console');

		return $this->response;

	}


	private function make_breadcrumb($physical_dir)
	{
		debug::on(false);

		$dir = $physical_dir;
		debug::variable($dir);

		$is_file_path = false;
		if (!Str::ends_with($dir, DIRECTORY_SEPARATOR))
		{
			$dir = dirname($dir);
			debug::variable($dir);

			$is_file_path = true;
		}
		debug::variable($is_file_path);

		$folders = array();
		if (Str::in_str($dir, DIRECTORY_SEPARATOR))
		{
			$folders = explode(DIRECTORY_SEPARATOR, $dir);
		}
		debug::variable($folders);

		$return = array();

		$path = '';
		if (is_array($folders))
		{
			foreach ($folders as $folder)
			{
				if (!empty($folder))
				{
					$path .= DIRECTORY_SEPARATOR.$folder;
					debug::variable($path);

					$return[] = array(
						'name' => $folder,
						'path' => $path.DIRECTORY_SEPARATOR,
					);
				}
			}
		}

		if ($is_file_path)
		{
			$file_name = basename($physical_dir);
			debug::variable($file_name);

			$return[] = array(
				'name' => $file_name,
				'path' => $physical_dir,
			);
		}

		debug::variable($return);

		return $return;
	}


}



?>