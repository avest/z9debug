<?php
//===================================================================
// z9Debug
//===================================================================
// SessionAction.php
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

class SessionDataAction
{
	public function delete_sessions($data_dir, $session_id)
	{
		debug::on(false);
		debug::variable($session_id);
		debug::variable($data_dir);

		if ($session_id == 'ALL')
		{
			$sessions = $this->get_session_data($data_dir);
			debug::variable($sessions);

			if (is_array($sessions))
			{
				foreach ($sessions as $session)
				{
					$session_id = $session['session_id'];
					debug::variable($session_id);

					$dir_path = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$session_id;
					debug::variable($dir_path);

					debug::string('delete_dir');
					File::delete_dir($dir_path);
				}
			}
		}
		elseif (!empty($session_id))
		{
			$dir_path = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$session_id;
			debug::variable($dir_path);

			File::delete_dir($dir_path);
		}

	}

	public function get_session_data($data_dir)
	{
		debug::on(false);
		debug::variable($data_dir);

		clearstatcache();

		$sessions = array();

		$unix_now = time();

		$dir_list = File::get_dir_dir_list($data_dir);
		debug::variable($dir_list);

		if (is_array($dir_list))
		{
			foreach ($dir_list as $session_id)
			{
				$session_dir = $data_dir.DIRECTORY_SEPARATOR.$session_id;
				debug::variable($session_dir);

				$last_mod_time = filemtime($session_dir);
				debug::variable($last_mod_time);

				// count number of requests in the session folder
				$session_dir_list = File::get_dir_dir_list($session_dir);
				debug::variable($session_dir_list);

				$request_count = count($session_dir_list);
				debug::variable($request_count);

				$session_id_parts = explode('_', $session_id);
				$session_name = '';
				if (isset($session_id_parts[1]))
				{
					unset($session_id_parts[0]);
					$session_name = implode('_', $session_id_parts);
					//$session_name = $session_id_parts[1];
				}

				// can't set to key to be last_mod_time, because it may be a duplicate
				// when doing ajax type calls.

				$session_key = $last_mod_time;
				while (isset($sessions[$session_key]))
				{
					$session_key += 1;
				}

				$latest_request_id = Action::_('Z9\Debug\Console\RequestData')->get_most_recent_request_id($session_dir);
				debug::variable($latest_request_id);

				$latest_request_data = Action::_('Z9\Debug\Console\RequestData')->get_request_data_for_request($session_id, $latest_request_id);
				debug::variable($latest_request_data);

				$sessions[$session_key] = array(
					'session_id' => $session_id,
					'session_dir' => $session_dir,
					'session_date' => $last_mod_time,
					'request_count' => $request_count,
					'session_name' => $session_name,
					'latest_request_id' => $latest_request_id,
					'latest_request_data' => $latest_request_data,
				);
			}
		}


		krsort($sessions);

		return $sessions;
	}

	public function get_most_recent_session_id($data_dir)
	{
		debug::on(false);
		debug::variable($data_dir);

		$session_id = '';

		$dir_list = File::get_dir_dir_list($data_dir);
		debug::variable($dir_list);

		if (is_array($dir_list))
		{
			rsort($dir_list);
			debug::variable($dir_list);

			if (isset($dir_list[0]))
			{
				$session_id = $dir_list[0];
			}
			debug::variable($session_id);
		}

		return $session_id;
	}

	public function update_session_last_mod_time($session_dir)
	{
		debug::on(false);
		debug::variable($session_dir);

		$dir_path = $session_dir.DIRECTORY_SEPARATOR.'.';
		debug::variable($dir_path);

		@touch($dir_path);
	}

	public function purge_old_sessions($session_id, $data_dir)
	{
		debug::on(false);
		debug::variable($session_id);
		debug::variable($data_dir);

		$dir_list = File::get_dir_dir_list($data_dir);
		debug::variable($dir_list);

		$unix_now = time();
		debug::variable($unix_now);

		if (is_array($dir_list))
		{
			foreach ($dir_list as $session_dir)
			{
				//$dir_path = $data_dir.DIRECTORY_SEPARATOR.$session_dir.DIRECTORY_SEPARATOR.'.';
				$dir_path = $data_dir.DIRECTORY_SEPARATOR.$session_dir;
				debug::variable($dir_path);

				$last_mod_time = filemtime($dir_path);
				debug::variable($last_mod_time);

				if ($unix_now - 86400 > $last_mod_time) // 1 day
				{
					// delete session
					debug::string("deleting session...");
					File::delete_dir($dir_path);
				}

			}
		}
	}

}

?>