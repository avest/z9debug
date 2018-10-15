<?php
//===================================================================
// z9Debug
//===================================================================
// console.php
// --------------------
// functions to support debug console
//
//       Date Created: 2005-04-23
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

include(Z9DEBUG_DIR.'/vendor/autoload.php');

use PhpParser\Error;
use PhpParser\ParserFactory;

function purge_old_requests($session_data, $session_dir)
{
	debug::on(false);

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

				delete_dir($dir_path);

				unset($session_data[$key]);
			}
		}
	}

	return $session_data;
}

function update_session_last_mod_time($session_dir)
{
	debug::on(false);
	debug::variable($session_dir);

	$dir_path = $session_dir.DIRECTORY_SEPARATOR.'.';
	debug::variable($dir_path);

	@touch($dir_path);
}

function purge_old_sessions($session_id, $data_dir)
{
	debug::on(false);
	debug::variable($session_id);
	debug::variable($data_dir);

	$dir_list = get_dir_dir_list($data_dir);
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
				delete_dir($dir_path);
			}

		}
	}
}

//----------------------------------------------
// retrieve data methods
//----------------------------------------------

function get_session_data($data_dir)
{
	debug::on(false);
	debug::variable($data_dir);

	clearstatcache();

	$sessions = array();

	$unix_now = time();

	$dir_list = get_dir_dir_list($data_dir);
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
			$session_dir_list = get_dir_dir_list($session_dir);
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

			$sessions[$session_key] = array(
				'session_id' => $session_id,
				'session_dir' => $session_dir,
				'session_date' => $last_mod_time,
				'request_count' => $request_count,
				'session_name' => $session_name,
			);
		}
	}


	krsort($sessions);

	return $sessions;
}

function get_request_data($session_dir)
{
	debug::on(false);
	debug::variable($session_dir);

	clearstatcache();

	$requests = array();

	$dir_list = get_dir_dir_list($session_dir);
	debug::variable($dir_list);

	if (is_array($dir_list))
	{
		foreach ($dir_list as $request_id)
		{
			$request_dir = $session_dir.DIRECTORY_SEPARATOR.$request_id;
			debug::variable($request_dir);

			$page_data = get_page_data($request_dir);
			debug::variable($page_data);

			$requests[$page_data['request_date']] = array(
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


function get_page_data($data_dir)
{
	debug::on(false);
	$file_path = $data_dir.DIRECTORY_SEPARATOR.'page_data.txt';
	$data = read_file($file_path);
	$data = unserialize($data);
	return $data;
}

function get_var_data($data_dir, $page=1)
{
	debug::on(false);
	$file_path = $data_dir.DIRECTORY_SEPARATOR.'var_data.txt';
	//$data = read_file($file_path);
	//$data = unserialize($data);

	$data = array();
	$line_count = 1;
	if (is_file($file_path))
	{
		if ($file = fopen($file_path, "r"))
		{
			while(!feof($file))
			{
				$line = fgets($file);
				//if (!empty($line))
				if (!empty($line) && $line_count == $page)
				{
					$unserialize_line = unserialize($line);
					if (is_array($unserialize_line))
					{
						$data = array_merge($data, $unserialize_line);
					}
				}
				$line_count++;
			}
			fclose($file);
		}
	}
	unset($line);

	return $data;
}




function display_value_lines($lines)
{
	debug::on(false);
	debug::variable($lines);

	$return = '';
	if (is_array($lines))
	{
		foreach ($lines as $line_key => $line)
		{
			$lines[$line_key] = htmlentities($lines[$line_key], ENT_QUOTES);
			//$lines[$line_key] = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $lines[$line_key]);
		}
	}

	debug::variable($lines);

	$lines_count = (is_array($lines)) ? count($lines) : 0;
	debug::variable($lines_count);

	if (is_array($lines) && count($lines) == 0)
	{
		$return  = '';
	}
	if (is_array($lines) && count($lines) == 1)
	{
		$return = $lines[0];
	}
	if (is_array($lines) && count($lines) > 0)
	{
		if (!is_resource($lines) && is_array($lines))
		{
			$return = implode("<br>", $lines);
		}
		else
		{
			return $lines;
		}
	}

	return $return;
}

function display_value_wrap($type)
{
	switch ($type)
	{
		case 'string':
			return "'";
			break;
		case 'not set':
			return "";
			break;
		case 'double':
			return "";
			break;
		case 'long':
			return "";
			break;
		case 'bool':
			return "";
			break;
		case 'unknown':
			return "";
			break;
	}
}


function get_timer_data($data_dir)
{
	debug::on(false);
	$file_path = $data_dir.DIRECTORY_SEPARATOR.'timer_data.txt';
	$data = read_file($file_path);
	$data = unserialize($data);
	return $data;
}

function get_sql_data($data_dir)
{
	debug::on(false);
	$file_path = $data_dir.DIRECTORY_SEPARATOR.'sql_data.txt';
	$data = read_file($file_path);
	$data = unserialize($data);

	if (is_array($data))
	{
		foreach ($data as $key => $query)
		{
			$data[$key]['sql'] = clean_sql($query['sql']);
			$data[$key]['from_class'] = clean_class($query['from_class']);
		}
	}

	if (isset($_POST['slow_queries']) && $_POST['slow_queries'] == '1')
	{
		if (is_array($data))
		{
			foreach ($data as $key => $query)
			{
				if ((int)$query['total'] < 1)
				{
					unset($data[$key]);
				}
			}
		}
	}


	return $data;
}

function clean_class($class)
{
	$last_pos = strrpos($class, '\\');
	if ($last_pos !== false)
	{
		$class = substr($class, $last_pos+1);
	}
	return $class;
}

function clean_sql($sql)
{
	$lines = explode("\n", $sql);
	debug::variable($lines);
	$min_tab_count = 9999;
	if (is_array($lines))
	{
		foreach ($lines as $line_key => $line)
		{
			$empty_line = trim($line);
			if (empty($empty_line))
			{
				unset($lines[$line_key]);
			}
			else
			{
				$tabs = explode("\t", $line);
				debug::variable($tabs);
				$tab_count = 0;
				if (is_array($tabs))
				{
					foreach ($tabs as $tab_key => $tab)
					{
						if (empty($tab))
						{
							$tab_count++;
						}
						else
						{
							break;
						}
					}
				}
				if ($tab_count < $min_tab_count)
				{
					$min_tab_count = $tab_count;
				}
			}
		}
	}
	//echo "min_tab_count=".$min_tab_count."<br>";

	if ($min_tab_count <> 9999 && $min_tab_count <> 0)
	{
		if (is_array($lines))
		{
			foreach ($lines as $line_key => $line)
			{
				$lines[$line_key] = substr($line, $min_tab_count);
			}
		}
	}

	$return = implode("\n", $lines);
	return $return;
}

function get_cms_data($data_dir)
{
	debug::on(false);
	$file_path = $data_dir.DIRECTORY_SEPARATOR.'cms_data.txt';
	$data = read_file($file_path);
	$data = unserialize($data);
	return $data;
}

function get_file_data($data_dir)
{
	debug::on(false);
	$file_path = $data_dir.DIRECTORY_SEPARATOR.'file_data.txt';
	$data = read_file($file_path);
	$data = unserialize($data);
	return $data;
}

function get_global_data($data_dir)
{
	debug::on(false);
	$file_path = $data_dir.DIRECTORY_SEPARATOR.'request_data.txt';
	$data = read_file($file_path);
	$data = unserialize($data);
	return $data;
}

function get_catalog($session_id, $request_id)
{
}

// originally from PHP Quick Profiler
// https://github.com/wufoo/PHP-Quick-Profiler
// Created by Ryan Campbell. Designed by Kevin Hale.
// Copyright (c) 2009 Infinity Box Inc.
function get_readable_file_size($size, $retstring = null)
{
	// adapted from code at http://aidanlister.com/repos/v/function.size_readable.php
	$sizes = array('bytes', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

	if ($retstring === null) { $retstring = '%01.2f %s'; }

	$lastsizestring = end($sizes);

	foreach ($sizes as $sizestring)
	{
		if ($size < 1024) { break; }
		if ($sizestring != $lastsizestring) { $size /= 1024; }
	}
	if ($sizestring == $sizes[0]) { $retstring = '%01d %s'; } // Bytes aren't normally fractional
	return sprintf($retstring, $size, $sizestring);
}

function friendly_memory($memory_size)
{
	debug::variable($memory_size);
	if ($memory_size >= 1048576)
	{
		$memory_size_ind_rnd = round(($memory_size/1048576),3) . " MB";
	}
	elseif ($memory_size >= 1024)
	{
		$memory_size_ind_rnd = round(($memory_size/1024),2) . " KB";
	}
	elseif ($memory_size >= 0)
	{
		$memory_size_ind_rnd = $memory_size . " bytes";
	}
	else
	{
		$memory_size_ind_rnd = "0 bytes";
	}

	return "$memory_size_ind_rnd";
}

function read_file($file_path)
{
	$output = "";

	// check that the file_path has a value
	if (strlen($file_path) > 0)
	{
		// check that the file_path file exists
		if (file_exists($file_path))
		{
			//----------------------------------
			// READ THE FILE
			//----------------------------------
			$fd = fopen($file_path, "r");
			$file_size = filesize($file_path);
			if ($file_size > 0)
			{
				$output = fread($fd, $file_size);
			}
			fclose($fd);
		}
	}

	return $output;
}

function remove_leading($input_string, $match_string)
{
	if (starts_with($input_string, $match_string))
	{
		return mid($input_string, strlen($match_string)+1);
	}
	else
	{
		return $input_string;
	}
}

function remove_trailing($input_string, $match_string)
{
	if (ends_with($input_string, $match_string))
	{
		return left($input_string, strlen($input_string) - strlen($match_string));
	}
	else
	{
		return $input_string;
	}
}

function ends_with($input_string, $match_string)
{
	$input_len = strlen($input_string);
	$match_len = strlen($match_string);
	if ($input_len > 0 && $match_len > 0)
	{
		if (right($input_string, $match_len) == $match_string)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}


function right($str, $howManyCharsFromRight)
{
	$strLen = strlen ($str);
	return substr ($str, $strLen - $howManyCharsFromRight, $strLen);
}

function starts_with($input_string, $match_string)
{
	$match_len = strlen($match_string);
	if ($match_string == left($input_string, $match_len))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function left($input_string, $str_length)
{
	$output_string = substr($input_string, 0, $str_length);
	return $output_string;
}

// first character is position 1
function mid($str, $start, $howManyCharsToRetrieve = 0)
{
	$start--;
	if ($howManyCharsToRetrieve === 0)
	{
		$howManyCharsToRetrieve = strlen($str) - $start;
	}

	$return_value = substr($str, $start, $howManyCharsToRetrieve);
	if (empty($return_value) && $return_value <> '0')
	{
		$return_value = '';
	}

	return $return_value;
}

// convert unix date
function convert_unix_date ($unixdate, $dateformat="yyyy-mm-dd hh:mm:ss", $now=false)
{
	$strDate = "";
	if ($now)
	{
 		$unixdate = time();
	}
	if (strlen($unixdate) > 0)
	{
		switch ($dateformat)
		{
			case "yyyy-mm-dd hh:mm:ss":
				$strDate = year($unixdate)."-";
				$strDate .= str_pad(mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
				$strDate .= str_pad(mday($unixdate), 2, "0", STR_PAD_LEFT)." ";
				$strDate .= str_pad(hour($unixdate), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(minute($unixdate), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(second($unixdate), 2, "0", STR_PAD_LEFT);
				break;
			case "yyyy-mm-dd":
				$strDate = year($unixdate)."-";
				$strDate .= str_pad(mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
				$strDate .= str_pad(mday($unixdate), 2, "0", STR_PAD_LEFT);
				break;
			case "mm/dd/yyyy hh:mm:ss am":
				$strDate  = str_pad(mon($unixdate), 2, "0", STR_PAD_LEFT)."/";
				$strDate .= str_pad(mday($unixdate), 2, "0", STR_PAD_LEFT)."/";
				$strDate .= year($unixdate)." ";
				$strDate .= str_pad(hour($unixdate, false), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(minute($unixdate), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(second($unixdate), 2, "0", STR_PAD_LEFT)." ";
				$strDate .= ((is_am($unixdate)) ? 'AM' : 'PM' );
				break;

			case "mm/dd/yyyy hh:mm:ss":
				$strDate  = str_pad(mon($unixdate), 2, "0", STR_PAD_LEFT)."/";
				$strDate .= str_pad(mday($unixdate), 2, "0", STR_PAD_LEFT)."/";
				$strDate .= year($unixdate)." ";
				$strDate .= str_pad(hour($unixdate), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(minute($unixdate), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(second($unixdate), 2, "0", STR_PAD_LEFT);
				break;

			case "hh:mm:ss":
				$strDate .= str_pad(hour($unixdate), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(minute($unixdate), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(second($unixdate), 2, "0", STR_PAD_LEFT);
				break;

			case "hh:mm:ss am":
				$hour = str_pad(hour($unixdate), 2, "0", STR_PAD_LEFT).":";
				$am_or_pm = 'AM';
				if ($hour >= 12)
				{
					$am_or_pm = 'PM';
				}
				if ($hour > 12)
				{
					$hour = $hour - 12;
					$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
					$am_or_pm = 'PM';
				}
				$strDate .= $hour;
				$strDate .= str_pad(minute($unixdate), 2, "0", STR_PAD_LEFT).":";
				$strDate .= str_pad(second($unixdate), 2, "0", STR_PAD_LEFT)." ";
				$strDate .= $am_or_pm;
				break;

			case "mm/dd/yyyy":
				$strDate  = str_pad(mon($unixdate), 2, "0", STR_PAD_LEFT)."/";
				$strDate .= str_pad(mday($unixdate), 2, "0", STR_PAD_LEFT)."/";
				$strDate .= year($unixdate);
				break;

			case "Mon d, yyyy":
				$strDate  = month_name($unixdate).' ';
				$strDate .= mday($unixdate).", ";
				$strDate .= year($unixdate);
				break;

			case "yyyy-mm-dd-hh-mm-ss":
				$strDate = year($unixdate)."-";
				$strDate .= str_pad(mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
				$strDate .= str_pad(mday($unixdate), 2, "0", STR_PAD_LEFT)."-";
				$strDate .= str_pad(hour($unixdate), 2, "0", STR_PAD_LEFT)."-";
				$strDate .= str_pad(minute($unixdate), 2, "0", STR_PAD_LEFT)."-";
				$strDate .= str_pad(second($unixdate), 2, "0", STR_PAD_LEFT);
				break;
		    case "yyyymmddhhmmss":
				$strDate = year($unixdate);
				$strDate .= str_pad(mon($unixdate), 2, "0", STR_PAD_LEFT);
				$strDate .= str_pad(mday($unixdate), 2, "0", STR_PAD_LEFT);
				$strDate .= str_pad(hour($unixdate), 2, "0", STR_PAD_LEFT);
				$strDate .= str_pad(minute($unixdate), 2, "0", STR_PAD_LEFT);
				$strDate .= str_pad(second($unixdate), 2, "0", STR_PAD_LEFT);
				break;
		}
	}
	return $strDate;
}

function year ($datetime=0) {
	// Returns the numeric year
	if ($datetime == 0) { $datetime = time(); }
	$date_array = getdate($datetime);
	return $date_array['year'];
}

function mon ($datetime=0)
{
	// Returns the numeric month
	if ($datetime == 0) { $datetime = time(); }
	$date_array = getdate($datetime);
	return $date_array['mon'];
}

function mday ($datetime=0)
{
	// Returns the numeric day of month
	if ($datetime == 0) { $datetime = time(); }
	$date_array = getdate($datetime);
	return $date_array['mday'];
}

function hour ($datetime=0)
{
	// Returns the numeric hour
	if ($datetime == 0) { $datetime = time(); }
	$date_array = getdate($datetime);
	return $date_array['hours'];
}

function minute ($datetime=0)
{
	// Returns the numeric minute
	if ($datetime == 0) { $datetime = time(); }
	$date_array = getdate($datetime);
	return $date_array['minutes'];
}

function second ($datetime=0)
{
	// Returns the numeric second
	if ($datetime == 0) { $datetime = time(); }
	$date_array = getdate($datetime);
	return $date_array['seconds'];
}

function get_dir_dir_list($dir_path)
{
	debug::on(false);

	$dir_list=array();
	if ($handle = @opendir($dir_path))
	{
		debug::variable($handle);
		while (false !== ($file = readdir($handle)))
		{
			debug::variable($file);
			if ($file <> '.' and $file <> '..' and @is_dir($dir_path.'/'.$file) )
			{
				if ($file <> '.git' && $file <> '.well-known')
				{
					$dir_list[] = $file;
				}
			}
	    }
	    closedir($handle);
	}
	debug::variable($dir_list);
	return $dir_list;
}

function delete_dir($dir_path)
{
	debug::on(false);
	debug::default_limit(100);
	debug::string('delete_dir()');
	debug::variable($dir_path);

	$success = false;

	if (@is_dir($dir_path))
	{
		$handle = opendir($dir_path);
		if ($handle)
		{
			while (false !== ($item = readdir($handle)))
			{
				debug::variable($item);
				if ($item != '.' && $item != '..')
				{
					if (@is_dir($dir_path.DIRECTORY_SEPARATOR.$item))
					{
						delete_dir($dir_path.DIRECTORY_SEPARATOR.$item);
						clearstatcache();
					}
					else
					{
						// add support for deleting a read only file on windows
						@chmod($dir_path.DIRECTORY_SEPARATOR.$item, 0777);
						clearstatcache();

						debug::string('unlink('.$dir_path.DIRECTORY_SEPARATOR.$item.')');
						@unlink($dir_path.DIRECTORY_SEPARATOR.$item);
						clearstatcache();
					}
				}
			}
			closedir($handle);
			clearstatcache();
			if (@rmdir($dir_path))
			{
				clearstatcache();
				$success = true;
			}
		}
	}
	else
	{
		$success = true;
	}
	return $success;
}

function is_valid_auth_token()
{
	debug::on(false);
	debug::string('is_valid_auth_token()');
	$cms_cookie_name = 'z9debug_token';
	$cms_cookie_value = '';
	$cms_user = '';
	$cms_cookie_issued = '';
	$cms_cookie_expired = '';
	$cms_cookie_hash = '';
	$cms_calc_hash = '';
	$cms_auth_secret_key = debug::get('secret');
	$cms_public_part = '';

	if (isset($_COOKIE[$cms_cookie_name]))
	{
		$cms_cookie_value = $_COOKIE[$cms_cookie_name];
		list($cms_user, $cms_cookie_issued, $cms_cookie_expired, $cms_cookie_hash) = explode(":", $cms_cookie_value, 4);
		$cms_public_part = $cms_user.":".$cms_cookie_issued.":".$cms_cookie_expired;
		$cms_calc_hash = md5($cms_auth_secret_key.":".md5($cms_public_part.":".$cms_auth_secret_key));
	}
	debug::variable($cms_cookie_name);
	debug::variable($cms_cookie_value);
	debug::variable($cms_auth_secret_key);
	debug::variable($cms_user);
	debug::variable($cms_cookie_issued);
	debug::variable($cms_cookie_expired);
	debug::variable($cms_cookie_hash);
	debug::variable($cms_public_part);
	debug::variable($cms_calc_hash);

	$is_valid_auth_token = false;
	if ($cms_calc_hash == $cms_cookie_hash and strlen($cms_cookie_hash) > 0)
	{
		$is_valid_auth_token = true;
		debug::variable($is_valid_auth_token);
	}
	else
	{
		debug::variable($is_valid_auth_token);
	}


	return $is_valid_auth_token;
}

function set_auth_token($username='')
{
	debug::on(false);
	$cms_cookie_name = 'z9debug_token';
	$cms_user = $username;
	$cms_auth_secret_key = debug::get('secret');

	$cms_cookie_issued = convert_unix_date(time(), $dateformat="yyyy-mm-dd-hh-mm-ss");
	$cms_cookie_expired = convert_unix_date(time()+31536000, $dateformat="yyyy-mm-dd-hh-mm-ss"); // 1 yr
	$cms_public_part = $cms_user.":".$cms_cookie_issued.":".$cms_cookie_expired;
	$cms_calc_hash = md5($cms_auth_secret_key.":".md5($cms_public_part.":".$cms_auth_secret_key));
	$cms_auth_token = $cms_public_part.":".$cms_calc_hash;

	if (debug::get('force_http'))
	{
		setcookie($cms_cookie_name, $cms_auth_token, time()+31536000,"/", null, false);
	}
	else
	{
		setcookie($cms_cookie_name, $cms_auth_token, time()+31536000,"/", null, true);
	}
	$_COOKIE[$cms_cookie_name] = $cms_auth_token;

	debug::variable($cms_cookie_name);
	debug::variable($cms_auth_secret_key);
	debug::variable($cms_user);
	debug::variable($cms_cookie_issued);
	debug::variable($cms_cookie_expired);
	debug::variable($cms_public_part);
	debug::variable($cms_calc_hash);

}

function post_to_url($post_values, $post_url, $user_agent='', $referer='', $cookies='', $timeout=0)
{
	$libcurl = false;
	if (function_exists('curl_init'))
	{
		$libcurl = true;
	}

	//--------------------------------------------------------
	// urlencode input fields, build data string to be posted
	//--------------------------------------------------------
	$data = '';
	if (is_array($post_values))
	{
		foreach($post_values as $key => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $key2 => $value2)
				{
					$data .= $key.'['.$key2."]=".urlencode($value2)."&";
				}
			}
			else
			{
				$data .= $key."=".urlencode($value)."&";
			}
		}
	}
	// strip off last amperstand
	$data = left($data, strlen($data)-1);

	//Replaces spaces with + for url formating. Would of used urlencode but it does
	//really weird things to the data.
	$data=str_replace(" ", "+", $data);

    // post data
	if ($libcurl)
	{
		// Use curl functions built into PHP
		//echo curl_version()."<br>";
		$ch = curl_init();

		//curl_setopt($ch, CURLOPT_SSLVERSION, 3);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

		curl_setopt ($ch, CURLOPT_URL, $post_url);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		if (strlen($timeout) > 0 && $timeout <> 0)
		{
			curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
		}
		$result_array = curl_exec ($ch);
		curl_close ($ch);
		$result_array = explode("\n", $result_array);
	}
	else
	{
		if (file_exists('/usr/bin/curl'))
		{
			$curl = '/usr/bin/curl --location --max-redirs 1';
		}
		else if (file_exists('/usr/local/bin/curl'))
		{
			$curl = "/usr/local/bin/curl --location --max-redirs 1";
		}

		if (strlen($user_agent) > 0)
		{
			$curl .= ' --user-agent '.$user_agent;
		}
		if (strlen($referer) > 0)
		{
			$curl .= ' --referer '.$referer;
		}
		if (strlen($cookies) > 0)
		{
			$curl .= ' --cookie '.$cookies;
		}
		if (strlen($timeout) > 0 && $timeout <> 0)
		{
			$curl .= ' --max-time '.$timeout;
		}

		exec("$curl -d \"$data\" $post_url", $result_array);
	}

	return $result_array;
}


$_DEBUG['variable_names'] = array();
$_DEBUG['session_id'] = '';
$_DEBUG['request_id'] = '';
$_DEBUG['file_path'] = '';
$_DEBUG['file_handle'] = '';

function get_variable_name($session_id, $request_id, $file_path, $line_number, $org_variable_name)
{
	// we are using a global variable here on purpose to cache a list of variable names already processed!
	global $_DEBUG;
	debug::on(false);

	debug::variable($session_id);
	debug::variable($request_id);
	debug::variable($file_path);
	debug::variable($line_number);
	debug::variable($org_variable_name);

	$post_variable_name = remove_leading($org_variable_name, '###EMPTY###');
	debug::variable($post_variable_name);

	if (isset($_DEBUG['variable_names'][$session_id][$request_id][$file_path][$line_number]))
	{
		$variable_name = $_DEBUG['variable_names'][$session_id][$request_id][$file_path][$line_number];
		$variable_name .= $post_variable_name;
		debug::variable($variable_name);

		return $variable_name;
	}
	else
	{

		$web_file_path = $file_path;
		debug::variable($web_file_path);

		$session_file_path = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.
			$session_id.DIRECTORY_SEPARATOR.$request_id.
			DIRECTORY_SEPARATOR.'files'.$web_file_path;
		debug::variable($session_file_path);


		// check if we can use the current file being processed from the last request...
		$use_file_content = false;
		if (
			$_DEBUG['session_id'] == $session_id &&
			$_DEBUG['request_id'] == $request_id &&
			$_DEBUG['file_path'] == $file_path
		)
		{
			$use_file_content = true;
		}

		// set $file_handle
		if ($use_file_content)
		{
			$file_handle = $_DEBUG['file_handle'];
		}
		else
		{
			// the new method should be faster
			// this methods adds approximately .00065 seconds per debug:variable call.
			// so, 1000 debug calls would add .65 seconds.
			if (is_file($session_file_path))
			{
				$file_handle = new SplFileObject($session_file_path);
			}
		}

		// set $file_line
		$file_line = '';
		if (!empty($file_handle))
		{
			$file_handle->seek($line_number-1);
			$file_line = $file_handle->current();
			$file_line = '<'.'?php '.$file_line.' ?'.'>';
			debug::variable($file_line);
		}

		//debug::constant(T_OBJECT_OPERATOR, 'T_OBJECT_OPERATOR');
		//debug::constant(token_name(310), 'token_name(310)');
		$variable_name = '';
		if (!empty($file_line))
		{
			$variable_name = parse_file_line_for_variable_name($file_line);
		}
	}

	// cache the variable name
	if (!empty($variable_name))
	{
		$_DEBUG['variable_names'][$session_id][$request_id][$file_path][$line_number] = $variable_name;
	}

	// return the variable name with the post_variable_name appended
	$variable_name .= $post_variable_name;
	debug::variable($variable_name);

	return $variable_name;
}


function parse_file_line_for_variable_name($file_line)
{

	// PREG_MATCH METHOD
	preg_match('/(.*?)(debug)(.*?)(::)(.*?)(variable)(.*\(?)(.*)(.*\)?)(.*?)(;)(.*?)/', $file_line, $matches);
	debug::variable($matches);

	$variable_name = $matches[7];
	$variable_name = remove_leading($variable_name, '(');
	$variable_name = remove_trailing($variable_name, ')');
	$comma_pos = strpos($variable_name, ',');
	if ($comma_pos > 0)
	{
		$variable_name = left($variable_name, $comma_pos);
	}


//	// NEW METHOD
//
//	$variable_name = '';
//	$debug_found = false;
//	$double_colon_found = false;
//	$variable_found = false;
//	$first_parentheses_found = false;
//	$first_key = 0;
//
//	//310=T_STRING
//	//312=T_VARIABLE
//	//358=T_CLASS
//	//363=T_OBJECT_OPERATOR
//	//376=T_OPEN_TAG
//	//378=T_CLOSE_TAG
//	//379=T_WHITESPACE
//	//384=T_DOUBLE_COLON
//
//	// The new method is to find the "debug::variable(" position.
//	// Then record everything up to the last ");"
//	// The stuff in the middle is the variable name
//
//	$tokens = token_get_all($file_line);
//	debug::variable($tokens);
//
//	// find $first_key
//	if (is_array($tokens))
//	{
//		foreach ($tokens as $key => $token)
//		{
//			debug::variable($key);
//			debug::variable($token);
//			if (!$debug_found)
//			{
//				if (isset($token[0]) && $token[0] == T_STRING && $token[1] == 'debug')
//				{
//					$debug_found = true;
//				}
//			}
//			else
//			{
//				if (!$double_colon_found)
//				{
//					if (isset($token[0]) && $token[0] == T_DOUBLE_COLON && $token[1] == '::')
//					{
//						$double_colon_found = true;
//					}
//				}
//				else
//				{
//					if (!$variable_found)
//					{
//						if (isset($token[0]) && $token[0] == T_STRING && $token[1] == 'variable')
//						{
//							$variable_found = true;
//						}
//					}
//					else
//					{
//						if (!$first_parentheses_found)
//						{
//							if ($token == '(')
//							{
//								$first_parentheses_found = true;
//							}
//						}
//						else
//						{
//							if ($first_key == 0)
//							{
//								$first_key = $key;
//							}
//						}
//					}
//				}
//			}
//		}
//	} // end set $first_key
//
//	debug::variable($first_key);
//
//
//	$last_semi_colon_found = false;
//	$last_parentheses_found = false;
//	$last_key = 0;
//
//
//	// find $last_key
//	if (is_array($tokens))
//	{
//		$tokens = array_reverse($tokens);
//	}
//
//	if (is_array($tokens))
//	{
//		foreach ($tokens as $key => $token)
//		{
//			debug::variable($key);
//			debug::variable($token);
//			if (!$last_semi_colon_found)
//			{
//				if ($token == ';')
//				{
//					$last_semi_colon_found = true;
//				}
//			}
//			else
//			{
//				if (!$last_parentheses_found)
//				{
//					if ($token == ')')
//					{
//						$last_parentheses_found = true;
//					}
//				}
//				else
//				{
//					if ($last_key == 0)
//					{
//						$last_key = $key;
//					}
//				}
//			}
//		}
//	} // end set $last_key
//
//	debug::variable($last_key);
//
//	$key_count = count($tokens);
//	debug::variable($key_count);
//
//	$last_key = $key_count - $last_key - 1;
//	debug::variable($last_key);
//
//	$tokens = array_reverse($tokens);
//
//	if (is_array($tokens))
//	{
//		foreach ($tokens as $key => $token)
//		{
//			if ($key >= $first_key && $key <= $last_key)
//			{
//				if (isset($token[1]))
//				{
//					$variable_name .= $token[1];
//				}
//				else
//				{
//					$variable_name .= $token;
//				}
//			}
//		}
//	}



//				// OLD METHOD
//	$get_next_t_string = false;
//	$prev_token = '';
//	$prev2_token = '';
//
//				//echo $token[0].'='.token_name($token[0])."<br>";
//				if ($token[0] == T_VARIABLE)
//				{
//					if (empty($variable_name))
//					{
//						$variable_name = remove_leading($token[1], '$');
//
//						if ($prev_token[0] == T_DOUBLE_COLON)
//						{
//							$prepend = $prev2_token[1].'::';
//							$variable_name = $prepend.$variable_name;
//						}
//					}
//				}
//				if ($token[0] == T_OBJECT_OPERATOR)
//				{
//					$append = '->';
//					$get_next_t_string = true;
//					$variable_name .= $append;
//				}
//				if ($token[0] == T_STRING)
//				{
//					if ($get_next_t_string)
//					{
//						$append = $token[1];
//						$get_next_t_string = false;
//						$variable_name .= $append;
//					}
//				}
//
//				$prev2_token = $prev_token;
//				$prev_token = $token;


	return $variable_name;

}

function get_dir_php_file_list($dir_path)
{
	$file_list=array();
	if ($handle = @opendir($dir_path))
	{
		while (false !== ($file = readdir($handle)))
		{
			if ($file <> '.' && $file <> '..' && !@is_dir($dir_path.'/'.$file) )
			{
				if (ends_with($file, '.php'))
				{
					$file_list[] = $file;
				}
			}
		}
		closedir($handle);
	}
	return $file_list;
}

function html($input_string)
{
	return htmlspecialchars($input_string,ENT_QUOTES);
}


function make_breadcrumb($physical_dir)
{
	debug::on(false);

	$dir = $physical_dir;
	debug::variable($dir);

	$is_file_path = false;
	if (!ends_with($dir, DIRECTORY_SEPARATOR))
	{
		$dir = dirname($dir);
		debug::variable($dir);

		$is_file_path = true;
	}
	debug::variable($is_file_path);

	$folders = array();
	if (in_str($dir, DIRECTORY_SEPARATOR))
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

function in_str($mystring, $findme)
{
	if (!empty($findme))
	{
		$pos = strpos($mystring, $findme);
		// Note our use of ===.
		if ($pos === false)
		{
			$return_value = false;
		}
		else
		{
			$return_value = true;
		}
	}
	else
	{
		$return_value = false;
	}
	return $return_value;
}

function get_micro_time()
{
	list($sec, $usec) = explode(" ", microtime());
	return ($sec + $usec);
}

function display_micro_time_diff($start_time, $end_time)
{
	debug::variable($start_time);
	debug::variable($end_time);
	$diff = $end_time - $start_time;
	if ($diff < 0.0001)
	{
		$return = "0.0000";
	}
	else
	{
		$return = substr(($diff), 0, 6);
	}
	debug::variable($return);

	return $return;
}

function parse_php_file($file_path)
{
	debug::on(false);
	debug::variable($file_path);

	$start_time = get_micro_time();

	$code = '';
	if (is_file($file_path))
	{
		$code = read_file($file_path);
	}
	//debug::variable($code);

	$code_len = strlen($code);
	debug::variable($code_len);

	// old versions of PHP don't allow this line
	//$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
	$parser_factory = new ParserFactory;
	$parser = $parser_factory->create(ParserFactory::PREFER_PHP7);

	try
	{
		$ast = $parser->parse($code);
		//debug::variable($ast);

		$ast_count = count($ast);
		debug::variable($ast_count);
	}
	catch (Error $error)
	{
		echo "Parse error: {$error->getMessage()}\n";
		return;
	}

	$result = array();

	if (is_array($ast))
	{
		foreach ($ast as $key => $value)
		{
			debug::variable($key);

			$class = get_class($value);
			debug::variable($class);

			// TOP LEVEL FUNCTIONS
			if ($class == 'PhpParser\Node\Stmt\Function_')
			{
				//debug::variable($value);

				$function_name = $value->name;
				//debug::variable($function_name);

				$function_line = $value->getLine();
				//debug::variable($function_line);

				//$result[$file_path]['-']['-'][$function_name] = $function_line;
				$result[] = array(
					'file_path' => $file_path,
					'namespace' => '',
					'class' => '',
					'function' => $function_name,
					'line_number' => $function_line,
				);
			}

			// TOP LEVEL NAMESPACE
			if ($class == 'PhpParser\Node\Stmt\Namespace_')
			{
				//debug::variable($value);

				$namespace_name = $value->name->toString();
				debug::variable($namespace_name);

				if (is_array($value->stmts))
				{
					foreach ($value->stmts as $value2)
					{
						$class2 = get_class($value2);
						//debug::variable($class2);

						// TOP LEVEL CLASSES WITHIN NAMESPACE
						if ($class2 == 'PhpParser\Node\Stmt\Class_')
						{
							//debug::variable($value2);

							$class_name = $value2->name;
							//debug::variable($class_name);

							if (is_array($value2->stmts))
							{
								foreach ($value2->stmts as $value3)
								{
									$class3 = get_class($value3);
									//debug::variable($class3);

									if ($class3 == 'PhpParser\Node\Stmt\ClassMethod')
									{
										//debug::variable($value3);

										$class_method = $value3->name;
										//debug::variable($class_method);

										$class_method_line = $value3->getLine();
										//debug::variable($class_method_line);

										//$result[$file_path][$namespace_name][$class_name][$class_method] = $class_method_line;
										$result[] = array(
											'file_path' => $file_path,
											'namespace' => $namespace_name,
											'class' => $class_name,
											'function' => $class_method,
											'line_number' => $class_method_line,
										);

									}
								}

							}


						}
					}
				}

			}


			// TOP LEVEL CLASS
			if ($class == 'PhpParser\Node\Stmt\Class_')
			{
				//debug::variable($value);

				$namespace_name = '';
				debug::variable($namespace_name);

				//debug::variable($value);

				$class_name = $value->name;
				//debug::variable($class_name);

				if (is_array($value->stmts))
				{
					foreach ($value->stmts as $value2)
					{
						$class2 = get_class($value2);
						//debug::variable($class2);

						if ($class2 == 'PhpParser\Node\Stmt\ClassMethod')
						{
							//debug::variable($value2);

							$class_method = $value2->name;
							//debug::variable($class_method);

							$class_method_line = $value2->getLine();
							//debug::variable($class_method_line);

							//$result[$file_path][$namespace_name][$class_name][$class_method] = $class_method_line;
							$result[] = array(
								'file_path' => $file_path,
								'namespace' => $namespace_name,
								'class' => $class_name,
								'function' => $class_method,
								'line_number' => $class_method_line,
							);

						}
					}
				}

			}


		}
	}


	$end_time = get_micro_time();

	$total_time = display_micro_time_diff($start_time, $end_time);
	debug::variable($total_time);

	debug::variable($result);

	return $result;
}

function save_toggle_data($data, $data_file)
{
	debug::on(false);
	debug::variable($data);
	debug::variable($data_file);

	//debug::set('force_enabled', true);
	//debug::set('force_suppress_output', true);
	//debug::set('force_on', array(
	//	'/classes/Channel/Bigcommerce/Controller/Index.php' => array(
	//		'Channel/Bigcommerce/Controller/Index::__invoke',
	//	),
	//));

	$data_content = "";
	$data_content .= "<"."?php\r\n";

	if (isset($data['force_enabled']) && $data['force_enabled'])
	{
		$data_content .= "debug::set('force_enabled', true);\r\n";
	}
	else
	{
		$data_content .= "debug::set('force_enabled', false);\r\n";
	}

	if (isset($data['force_suppress_output']) && $data['force_suppress_output'])
	{
		$data_content .= "debug::set('force_suppress_output', true);\r\n";
	}
	else
	{
		$data_content .= "debug::set('force_suppress_output', false);\r\n";
	}

	$data_content .= "debug::set('force_on', ";
	$data_content .= convert_var_value_to_string($data['force_on']);
	$data_content .= ");\r\n";

	$data_content .= "?".">\r\n";
	debug::variable($data_content);


	// if file exist, let's rename it to a backup copy

	$data_file_name_base = basename($data_file);
	debug::variable($data_file_name_base);

	$backup_data_file_name_base = "bak.".$data_file_name_base;
	debug::variable($backup_data_file_name_base);

	$backup_data_file = dirname($data_file).DIRECTORY_SEPARATOR.$backup_data_file_name_base;
	debug::variable($backup_data_file);

	if (file_exists($backup_data_file))
	{
		chmod($backup_data_file, 0777);
		unlink($backup_data_file);
	}
	if (file_exists($data_file))
	{
		rename($data_file, $backup_data_file);
	}

	// write file
	$fd = fopen($data_file, "w");
	fwrite($fd, $data_content);
	fclose($fd);
}

function convert_var_to_string($var_value, $var_name)
{
	debug::on(false);
	debug::variable($var_value);
	debug::variable($var_name);

	$return = '';
	$return .= '$'.$var_name.' = ';
	$return .= stripslashes(var_export($var_value, true));
	$return .= ";\r\n";
	return $return;
}

function convert_var_value_to_string($var_value)
{
	debug::on(false);
	debug::variable($var_value);

	$return = '';
	$return .= stripslashes(var_export($var_value, true));
	$return .= "\r\n";
	return $return;
}

function get_on_file_function_value($namespace, $class, $function)
{
	debug::on(false);
	debug::variable($namespace);
	debug::variable($class);
	debug::variable($function);

	$on_file_function_value = '';
	if (!empty($namespace))
	{
		$on_file_function_value .= $namespace;
	}
	if (!empty($namespace) && !empty($class))
	{
		$on_file_function_value .= '\\';
	}
	if (!empty($class))
	{
		$on_file_function_value .= $class;
	}
	if (!empty($class))
	{
		$on_file_function_value .= '::';
	}
	if (!empty($function))
	{
		$on_file_function_value .= $function;
	}
	if (empty($on_file_function_value))
	{
		$on_file_function_value = '-';
	}
	$on_file_function_value = str_replace('\\', '/', $on_file_function_value);
	debug::variable($on_file_function_value);

	return $on_file_function_value;
}

function parse_class_name($class)
{
	debug::on(false);
	debug::variable($class);

	$namespace = '';
	$class_name = '';

	if (in_str($class, '\\'))
	{
		$parts = explode('\\', $class);
		debug::variable($parts);

		if (is_array($parts))
		{
			$parts_count = count($parts);
			debug::variable($parts_count);

			if ($parts_count > 1)
			{
				$class_name = $parts[$parts_count - 1];
				debug::variable($class_name);

				unset($parts[$parts_count - 1]);
			}
			else
			{
				$class_name = $class;
				debug::variable($class_name);
			}
		}

		$namespace = implode('\\', $parts);
	}
	debug::variable($namespace);

	return array(
		'namespace' => $namespace,
		'class_name' => $class_name,
	);
}

function is_toggled_on($file_path, $namespace, $class, $function)
{
	debug::on(false);
	debug::variable($file_path);
	debug::variable($namespace);
	debug::variable($class);
	debug::variable($function);

	$force_on = debug::get('force_on');
	debug::variable($force_on);

	$force_on_path = remove_leading($file_path, $_SERVER['DOCUMENT_ROOT']);
	debug::variable($force_on_path);

	// we need the current on_file_function settings
	$on_file_functions = array();
	if (isset($force_on[$force_on_path]))
	{
		$on_file_functions = $force_on[$force_on_path];
	}
	debug::variable($on_file_functions);

	// are we toggling on or off?

	// Channel/Bigcommerce/Controller/Index::__invoke
	// namespace = Channel\Bigcommerce\Controller
	// class = Index
	$on_file_function_value = get_on_file_function_value($namespace, $class, $function);
	debug::variable($on_file_function_value);

	$is_on = false;
	if (in_array($on_file_function_value, $on_file_functions))
	{
		$is_on = true;
	}
	debug::variable($is_on);

	return $is_on;
}

?>