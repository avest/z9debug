<?php
//===================================================================
// z9Debug
//===================================================================
// OnOffCount.php
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


class OnOffCount
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
			case 'on_off_count.php':
				return $this->display_on_off_count();
				break;
		}
	}


	public function display_on_off_count()
	{
		debug::on(false);

		$on_off_count = 0;

		$force_on = debug::get('force_on');
		debug::variable($force_on);

		if (is_array($force_on))
		{
			foreach ($force_on as $file_name => $file_name_settings)
			{
				if (is_array($file_name_settings))
				{
					foreach ($file_name_settings as $file_index => $file_setting)
					{
						$on_off_count++;
					}
				}
			}
		}

		$this->response->setVars(array(
			'on_off_count' => $on_off_count,
		));
		$this->response->setView('on_off_count.tpl.php', 'Z9\Debug\Console');

		return $this->response;


	}

}



?>