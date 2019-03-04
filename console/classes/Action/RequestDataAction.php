<?php
//===================================================================
// z9Debug
//===================================================================
// RequestAction.php
// --------------------
//
//       Date Created: 2018-10-22
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================


namespace Z9\Debug\Console\Action;

use debug;
use Facade\File;
use Facade\Action;
use Facade\Config;

class RequestDataAction
{
	public function get_most_recent_request_id($session_dir)
	{
		debug::on(false);
		debug::variable($session_dir);

		$request_id = '';

		// find most recent request for session
		$dir_list = File::get_dir_dir_list($session_dir);
		if (is_array($dir_list))
		{
			rsort($dir_list);
			debug::variable($dir_list);

			if (isset($dir_list[0]))
			{
				$request_id = $dir_list[0];
			}
			debug::variable($request_id);
		}

		return $request_id;
	}

	public function get_request_data_for_request($session_id, $request_id)
	{
		debug::on(false);
		debug::variable($session_id);
		debug::variable($request_id);

		$session_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$session_id;
		debug::variable($session_dir);

		$request_dir = $session_dir.DIRECTORY_SEPARATOR.$request_id;
		debug::variable($request_dir);

		$page_data = Action::_('Z9\Debug\Console\PageData')->get_page_data($request_dir);
		debug::variable($page_data);

		$request_data = array(
			'session_id' => $session_id,
			'request_id' => $request_id,
			'request_full_url' => $page_data['request_full_url'],
			'request_url_path' => $page_data['request_url_path'],
			'request_date' => $page_data['request_date'],
		);
		debug::variable($request_data);

		return $request_data;
	}


	public function get_request_data($session_dir)
	{
		debug::on(false);
		debug::variable($session_dir);

		clearstatcache();

		$requests = array();

		$dir_list = File::get_dir_dir_list($session_dir);
		debug::variable($dir_list);

		if (is_array($dir_list))
		{
			sort($dir_list);
		}

		if (is_array($dir_list))
		{
			foreach ($dir_list as $request_id)
			{
				$request_dir = $session_dir.DIRECTORY_SEPARATOR.$request_id;
				debug::variable($request_dir);

				$page_data = Action::_('Z9\Debug\Console\PageData')->get_page_data($request_dir);
				debug::variable($page_data);

				$requests[] = array(
					'request_id' => $request_id,
					'request_full_url' => $page_data['request_full_url'],
					'request_url_path' => $page_data['request_url_path'],
					'request_date' => $page_data['request_date'],
				);
			}
		}

		krsort($requests);

		return $requests;
	}

	public function purge_old_requests($session_data, $session_dir)
	{
		debug::on(false);
		debug::variable($session_data);
		debug::variable($session_dir);

		$max_request_count = 100;

		$unix_now = time();
		debug::variable($unix_now);

		$count = 0;
		if (is_array($session_data))
		{
			foreach ($session_data as $key => $request)
			{
				debug::variable($request);

				$count++;

				if ($unix_now - 86400 > $request['request_date'] || $count > $max_request_count)
				{
					$dir_path = $session_dir.DIRECTORY_SEPARATOR.$request['request_id'];
					debug::variable($dir_path);

					File::delete_dir($dir_path);

					unset($session_data[$key]);
				}
			}
		}

		return $session_data;
	}

	public function delete_requests($session_id, $request_id)
	{
		debug::on(false);
		debug::variable($session_id);
		debug::variable($request_id);

		$data_dir = Config::get('path.debug.data_dir');
		debug::variable($data_dir);

		$session_dir = $data_dir.DIRECTORY_SEPARATOR.$session_id;
		debug::variable($session_dir);

		if ($request_id == 'ALL')
		{
			$requests = Action::_('Z9\Debug\Console\RequestData')->get_request_data($session_dir);
			debug::variable($requests);

			if (is_array($requests))
			{
				foreach ($requests as $request)
				{
					$request_id = $request['request_id'];
					debug::variable($request_id);

					$dir_path = $session_dir.DIRECTORY_SEPARATOR.$request_id;
					debug::variable($dir_path);

					File::delete_dir($dir_path);
				}
			}
		}
		elseif (!empty($request_id))
		{
			$dir_path = $session_dir.DIRECTORY_SEPARATOR.$request_id;
			debug::variable($dir_path);

			File::delete_dir($dir_path);
		}
	}

}

?>