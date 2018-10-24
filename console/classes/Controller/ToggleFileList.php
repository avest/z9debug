<?php
//===================================================================
// z9Debug
//===================================================================
// ToggleFileList.php
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


class ToggleFileList
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
			case 'toggle_file_list.php':
				$physical_path = $_SERVER['DOCUMENT_ROOT'];
				if (isset($_POST['physical_path']))
				{
					$physical_path = $_POST['physical_path'];
				}
				debug::variable($physical_path);

				return $this->display_toggle_file_list($physical_path);

				break;
		}
	}

	public function display_toggle_file_list($physical_path)
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

		$dir_path = $physical_path;
		if (!Str::ends_with($dir_path, DIRECTORY_SEPARATOR))
		{
			$dir_path = dirname($dir_path).DIRECTORY_SEPARATOR;
		}
		debug::variable($dir_path);

		$dir_file_list = $this->get_dir_php_file_list($dir_path);
		sort($dir_file_list);
		debug::variable($dir_file_list);


		$this->response->setVars(array(
			'dir_file_list' => $dir_file_list,
			'physical_path' => $physical_path,
			'dir_path' => $dir_path,
		));
		$this->response->setView('toggle_file_list.tpl.php', 'Z9\Debug\Console');

		return $this->response;

	}

	private function get_dir_php_file_list($dir_path)
	{
		$file_list=array();
		if ($handle = @opendir($dir_path))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file <> '.' && $file <> '..' && !@is_dir($dir_path.'/'.$file) )
				{
					if (Str::ends_with($file, '.php'))
					{
						$file_list[] = $file;
					}
				}
			}
			closedir($handle);
		}
		return $file_list;
	}


}



?>