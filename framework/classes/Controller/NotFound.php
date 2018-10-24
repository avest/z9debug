<?php
//===================================================================
// Z9 Framework
//===================================================================
// NotFound.php
// --------------------
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

namespace Z9\Framework\Controller;

use debug;
use Mlaphp\Request;
use Mlaphp\Response;
use Facade\Dependency;

class NotFound
{
	/** @var Request  */
	protected $request;
	/** @var Response  */
	protected $response;

	public function __construct()
	{
		$this->request = Dependency::inject('Request');
		$this->response = Dependency::inject('Response');
	}

	public function __invoke()
	{
		$url_path = parse_url(
			$this->request->_SERVER['REQUEST_URI'],
			PHP_URL_PATH
		);

		$this->response->setVars(array(
			'url_path' => $url_path,
		));

		$this->response->setView('not_found.tpl.php', 'Z9\Framework');

		return $this->response;
	}
}

?>