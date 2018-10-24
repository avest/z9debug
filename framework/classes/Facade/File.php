<?php
//===================================================================
// Z9 Framework
//===================================================================
// File.php
// --------------------
//       Date Created: 2005-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================


// function append_file($output, $to_file)
// function build_dir_list($directory, $recursive=false)
// function change_root_path($old_path, $new_path, $org_full_path)
// function copy_dir($from_dir, $to_dir)
// function copy_file($from_file, $to_file)
// function create_dir($dir_name, $dir_mod=0777)
// function create_file($to_file)
// function delete_dir($dir_path)
// function delete_file($to_file)
// function dirsort($a, $b)
// function file_exists_in_include_path($filename)
// function friendly_file_size($file_size)
// function get_absolute_path($path)
// function get_decimal_mod($path)
// function get_dir_dir_list($dir_path)
// function get_dir_file_list($dir_path)
// function get_dir_list($directory, $recursive=false, $sort=true, $full_path=false)
// function get_file_extension($file_name)
// function get_file_perms($file)
// function get_meta_values($from_file)
// function is_dir_empty($directory)
// function ModeOctal2rwx($ModeOctal)
// function ModeRWX2Octal($Mode_rwx)
// function move_file($from_file, $to_file)
// function no_leading_slash($path)
// function no_trailing_slash($path)
// function parent_dir($path, $levels=1)
// function recursive_mkdir($make_folder)
// function read_file($file_path)
// function set_path_relative_to_script($file_path)
// function sort_dir_array($dir_array)
// function strip_path($strip_path, $full_path)
// function strip_site_path ($strFilePath)
// function URLFriendlyText($SiteURL)
// function write_file($output, $to_file)

namespace Facade;

use debug;
use Facade\Config;
use Facade\Str;

class File
{
	public function _construct()
	{
	}

	public static function append_file($output, $to_file)
	{
		//----------------------------------
		// APPEND THE FILE
		//----------------------------------
		if (strlen($output) > 0)
		{
			// Make directory if it doesn't exist.
			$dir_name = dirname($to_file);
			if (!@is_dir($dir_name))
			{
				//echo "MKDIR NAME: ".$dir_name."<br>";
				//umask(0000);
				self::create_dir($dir_name, 0777);
			}

			// write the file
			$fd = fopen($to_file, "a");
			$bytes_written = fwrite($fd, $output);
			fclose ($fd);
		}

		if (file_exists($to_file))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function build_dir_list($directory, $recursive=false)
	{
	//	$dir_list = array();
	//	if ($handle = @opendir($dir_path))
	//	{
	//		while (false !== ($file = readdir($handle)))
	//		{
	//			if ($file <> '.' && $file <> '..')
	//			{
	//				$dir_list[] = array(
	//					'name' => $file,
	//					'is_dir' => @is_dir($dir_path.'/'.$file)
	//				);
	//			}
	//		}
	//	}
	//	closedir($handle);
	//	return $dir_list;

		$array_items = array();
		if ($handle = @opendir($directory))
		{
			while (false !== ($file = readdir($handle)))
			{
				if ($file != "." && $file != "..")
				{
					if (@is_dir($directory. "/" . $file))
					{
						if($recursive)
						{
							$array_items = array_merge($array_items, self::build_dir_list($directory. "/" . $file, $recursive));
						}
						$file = $directory . "/" . $file.'/';
						$array_items[] = preg_replace("/\/\//si", "/", $file);
					}
					else
					{
						$file = $directory . "/" . $file;
						$array_items[] = preg_replace("/\/\//si", "/", $file);
					}
				}
			}
			closedir($handle);
		}

		return $array_items;
	}

	public static function change_root_path($old_path, $new_path, $org_full_path)
	{
		$old_path_len = strlen($old_path);
		$new_full_path = $new_path.substr($org_full_path, $old_path_len);
		return $new_full_path;
	}

	public static function copy_dir($from_dir, $to_dir)
	{
		if (!Str::last_char($from_dir) == '/')
		{
			$from_dir .= '/';
		}
		debug::variable($from_dir, 'from_dir');

		if (!Str::last_char($to_dir) == '/')
		{
			$to_dir .= '/';
		}
		debug::variable($to_dir, 'to_dir');


		if (@is_dir($from_dir) && @is_dir($to_dir))
		{
			$recursive = true;
			$sort = true;
			$full_path = true;
			$from_dir_list = self::get_dir_list($from_dir, $recursive, $sort, $full_path);
			debug::variable($from_dir_list, 'from_dir_list');

			if (is_array($from_dir_list))
			{
				foreach($from_dir_list as $key => $from_dir_item)
				{
					debug::variable($from_dir_item, 'from_dir_item');

					// if directory
					if (Str::last_char($from_dir_item) == '/')
					{
						$new_dir_mod = self::get_decimal_mod($from_dir_item);
						debug::variable($new_dir_mod, 'new_dir_mod');

						$new_dir_path = $to_dir.self::strip_path($from_dir, $from_dir_item);
						debug::variable($new_dir_path, 'new_dir_path');
						self::create_dir($new_dir_path, $new_dir_mod);
					}
					else
					{
						$copy_from = $from_dir_item;
						$copy_to = $to_dir.self::strip_path($from_dir, $from_dir_item);
						debug::variable($copy_from, 'copy_from');
						debug::variable($copy_to, 'copy_to');
						self::copy_file($copy_from, $copy_to);
					}
				}
			}
		}
	}

	public static function copy_file($from_file, $to_file)
	{
		if (file_exists($from_file))
		{
			// Make directory if it doesn't exist.
			$dir_name = dirname($to_file);
			if (!@is_dir($dir_name))
			{
				//echo "MKDIR NAME: ".$dir_name."<br>";
				//umask(0000);
				self::create_dir($dir_name, 0777);
			}

			copy($from_file, $to_file);
		}

		if (file_exists($to_file))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function create_dir($dir_name, $dir_mod=0777)
	{
		debug::string("create_dir()");
		debug::variable($dir_name, 'dir_name');
		debug::variable($dir_mod, 'dir_mod');

		// make sure that path is using all forward slashes
		$dir_name = str_replace('\\', '/', $dir_name);
		debug::variable($dir_name, 'dir_name');

		$dir_name_array = explode('/', $dir_name);
		debug::variable($dir_name_array, 'dir_name_array');

		$check_dir = "";
		if (isset($dir_name_array))
		{
			foreach ($dir_name_array as $key => $dir_part)
			{
				debug::variable($dir_part, 'dir_part');
				if (strlen(trim($dir_part)) > 0)
				{
					if (Str::ends_with($dir_part, ':'))
					{
						$check_dir .= $dir_part;
					}
					else
					{
						$check_dir .= "/" . $dir_part;
					}
					debug::variable($check_dir, 'check_dir');

					// this code right here will generate open_basedir errors
					// without the @ symbol on the functions
					// when the open_basedir setting is restricted
					if (!@is_dir($check_dir))
					{
						//echo "MKDIR ".$check_dir."<br>";
						$old_umask = umask();
						umask(0);
						@mkdir($check_dir, $dir_mod);
						umask($old_umask);
					}
				}
			}
		}

		if (@is_dir($dir_name))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function create_file($to_file)
	{
		// Make directory if it doesn't exist.
		$dir_name = dirname($to_file);
		if (!@is_dir($dir_name))
		{
			//echo "MKDIR NAME: ".$dir_name."<br>";
			umask(0000);
			self::create_dir($dir_name, 0777);
		}

		// create file
		$fd = fopen($to_file, "w");
		//$bytes_written = fwrite ($fd, '');
		fclose($fd);

		if (file_exists($to_file))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function create_file_auth($file_name, $repo)
	{
		$auth = md5($file_name.Config::get('file.auth_secret').$repo);
		debug::variable($auth, 'auth');
		return $auth;
	}



	public static function delete_dir($dir_path)
	{
		debug::variable($dir_path, 'dir_path');

		if (@is_dir($dir_path))
		{
			debug::string("dir_path found");
			$handle = opendir($dir_path);
			if ($handle)
			{
				while (false !== ($item = readdir($handle)))
				{
					if ($item != '.' && $item != '..')
					{
						if (@is_dir($dir_path.'/'.$item))
						{
							self::delete_dir($dir_path.'/'.$item);
						}
						else
						{
							// add support for deleting a read only file on windows
							@chmod($dir_path.'/'.$item, 0777);

							unlink($dir_path.'/'.$item);
						}
					}
				}
				closedir($handle);
				if (rmdir($dir_path))
				{
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

	public static function delete_file($to_file)
	{
		if (file_exists($to_file))
		{
			@chmod($to_file, 0777);
			unlink($to_file);
		}
		if (file_exists($to_file))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/*
	 * Search if file is available in include_path
	 * This code is publicly available on php.net (2/11/2011)
	*/
	public static function file_exists_in_include_path($filename)
	{
		if (substr($filename, 0, 1) <> '/')
		{
			$filename = '/'.$filename;
		}

		debug::variable($filename, 'filename');

		if (function_exists("get_include_path"))
		{
			$include_path = get_include_path();
		}
		elseif (false !== ($ip = ini_get("include_path")))
		{
			$include_path = $ip;
		}
		else
		{
			return false;
		}
		debug::variable($include_path, 'include_path');

		if (false !== strpos($include_path, PATH_SEPARATOR))
		{
			debug::string("PATH_SEPARATOR found.");
			if (false !== ($temp = explode(PATH_SEPARATOR, $include_path)) && count($temp) > 0)
			{
				debug::variable($temp, 'temp');
				for ($n = 0; $n < count($temp); $n++)
				{
					if (false !== @file_exists($temp[$n] . $filename))
					{
						debug::string($filename." found.");
						return true;
					}
				}
				debug::string($filename." NOT found.");
				return false;
			}
			else
			{
				debug::string($filename." NOT found.");
				return false;
			}
		}
		elseif (!empty($include_path))
		{
			debug::string("PATH_SEPARATOR NOT found.");

			if (false !== @file_exists($include_path))
			{
				debug::string($filename." found.");
				return true;
			}
			else
			{
				debug::string($filename." NOT found.");
				return false;
			}
		}
		else
		{
			debug::string("include_path is empty");
			return false;
		}
	}

	public static function friendly_file_size($file_size)
	{
		if ($file_size >= 1048576)
		{
			$file_size_ind_rnd = round(($file_size/1048576),2) . " MB";
		}
		elseif ($file_size >= 1024)
		{
			$file_size_ind_rnd = round(($file_size/1024),2) . " KB";
		}
		elseif ($file_size >= 0)
		{
			$file_size_ind_rnd = $file_size . " bytes";
		}
		else
		{
			$file_size_ind_rnd = "0 bytes";
		}

		return "$file_size_ind_rnd";
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

	// path must start with a /
	// if path end with a /, then returned path will end with a /
	public static function get_absolute_path($path)
	{
		debug::string("get_absolute_path()");

		$org_path = $path;
		debug::variable($org_path, 'org_path');

		$trailing_slash = Str::ends_with($path, DIRECTORY_SEPARATOR);
		debug::variable($trailing_slash, 'trailing_slash');

		$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
		debug::variable($path, 'path');

		$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
		debug::variable($parts, 'parts');

		$absolutes = array();
		foreach ($parts as $part) {
			if ('.' == $part) continue;
			if ('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		debug::variable($absolutes, 'absolutes');

		if ($org_path == '/')
		{
			$new_path = $org_path;
		}
		else
		{
			if ($trailing_slash)
			{
				$new_path = DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $absolutes).DIRECTORY_SEPARATOR;
			}
			else
			{
				$new_path = DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $absolutes);
			}
		}

		debug::variable($new_path, 'new_path');
		return $new_path;

	}

	// return decimal representation of mod
	public static function get_decimal_mod($path)
	{
		if (file_exists($path))
		{
			// get fileperms value
			$file_perms = fileperms($path);
			debug::variable($file_perms, 'file_perms');

			// get octal value
			$file_perms = sprintf('%o', $file_perms);
			debug::variable($file_perms, 'file_perms');

			// strip off leading digit
			$file_perms = substr($file_perms, -4);
			debug::variable($file_perms, 'file_perms');

			// add leading zero if needed
			if (Str::first_char($file_perms) <> '0')
			$file_perms = '0'.$file_perms;
			debug::variable($file_perms, 'file_perms');

			// convert back to decimal
			$file_perms = octdec($file_perms);
			debug::variable($file_perms, 'file_perms');

			return $file_perms;
		}
		else
		{
			return false;
		}
	}

	public static function get_dir_list($directory, $recursive=false, $sort=true, $full_path=false)
	{
		debug::on(false);
		debug::variable($directory);
		debug::variable($recursive);
		debug::variable($sort);
		debug::variable($full_path);

		$directory = preg_replace("/\/\//si", "/", $directory);
		debug::variable($directory);

		if (Str::last_char($directory) == '/')
		{
			$dir_path = $directory;
		}
		else
		{
			$dir_path = $directory.'/';
		}
		debug::variable($dir_path, 'dir_path');

		$array_items = self::build_dir_list($directory, $recursive);
		debug::variable($array_items);

		if (!$full_path)
		{
			if (is_array($array_items))
			{
				foreach($array_items as $key => $array_item)
				{
					debug::variable($array_item, 'array_item');
					$array_items[$key] = Str::remove_leading($array_item, $dir_path);
				}
			}
			debug::variable($array_items, 'array_items');
		}

		if ($sort && is_array($array_items) && !empty($array_items))
		{
			$array_items = self::sort_dir_array($array_items);
			debug::variable($array_items, 'array_items');
		}

		return $array_items;
	}

	// returns file extension, including the dot
	public static function get_file_extension($file_name)
	{
		// find last period
		$last_period_pos = strrpos($file_name, ".");
		if ($last_period_pos === false)
		{
			$file_ext = "";
		}
		else
		{
			$file_ext = substr($file_name, $last_period_pos);
		}
		return $file_ext;
	}

	public static function get_file_perms($file)
	{
		$return = array();

		if (is_file($file) || is_dir($file))
		{

			$perms = fileperms($file);

			if (($perms & 0xC000) == 0xC000) {
				// Socket
				$info = 's';
			} elseif (($perms & 0xA000) == 0xA000) {
				// Symbolic Link
				$info = 'l';
			} elseif (($perms & 0x8000) == 0x8000) {
				// Regular
				$info = '-';
			} elseif (($perms & 0x6000) == 0x6000) {
				// Block special
				$info = 'b';
			} elseif (($perms & 0x4000) == 0x4000) {
				// Directory
				$info = 'd';
			} elseif (($perms & 0x2000) == 0x2000) {
				// Character special
				$info = 'c';
			} elseif (($perms & 0x1000) == 0x1000) {
				// FIFO pipe
				$info = 'p';
			} else {
				// Unknown
				$info = 'u';
			}

			$return['file_type'] = $info;

			// Owner
			$return['owner_read'] = (($perms & 0x0100) ? 'r' : '-');
			$return['owner_write'] = (($perms & 0x0080) ? 'w' : '-');
			$return['owner_execute'] = (($perms & 0x0040) ?
						(($perms & 0x0800) ? 's' : 'x' ) :
						(($perms & 0x0800) ? 'S' : '-'));

			// Group
			$return['group_read'] = (($perms & 0x0020) ? 'r' : '-');
			$return['group_write'] = (($perms & 0x0010) ? 'w' : '-');
			$return['group_execute'] = (($perms & 0x0008) ?
						(($perms & 0x0400) ? 's' : 'x' ) :
						(($perms & 0x0400) ? 'S' : '-'));

			// World
			$return['world_read'] = (($perms & 0x0004) ? 'r' : '-');
			$return['world_write'] = (($perms & 0x0002) ? 'w' : '-');
			$return['world_execute'] = (($perms & 0x0001) ?
						(($perms & 0x0200) ? 't' : 'x' ) :
						(($perms & 0x0200) ? 'T' : '-'));

			return $return;
		}
		else
		{
			return false;
		}
	}

	public static function get_dir_dir_list($dir_path)
	{
		$dir_list=array();
		if ($handle = @opendir($dir_path))
		{
			debug::variable($handle, 'handle');
			while (false !== ($file = readdir($handle)))
			{
				debug::variable($file, 'file');
				if ($file <> '.' && $file <> '..' && @is_dir($dir_path.'/'.$file) )
				{
					$dir_list[] = $file;
				}
			}
			closedir($handle);
		}
		debug::variable($dir_list, 'dir_list');
		return $dir_list;
	}

	public static function get_dir_file_list($dir_path)
	{
		$file_list=array();
		if ($handle = @opendir($dir_path))
		{
		    while (false !== ($file = readdir($handle)))
			{
				if ($file <> '.' and $file <> '..' and !@is_dir($dir_path.'/'.$file) )
				{
					$file_list[] = $file;
				}
			}
			closedir($handle);
		}
		return $file_list;
	}

	public static function get_meta_values($from_file)
	{
		$meta_tag_array = array();
		if (file_exists(set_path_relative_to_script($from_file)))
		{
			$meta_tag_array = get_meta_tags(set_path_relative_to_script($from_file), 1);
		}
		return $meta_tag_array;
	}

	public static function is_dir_empty($directory)
	{
		if (is_dir($directory))
		{
			if ($handle = @opendir($directory))
			{
				while (false !== ($file = readdir($handle)))
				{
					if ($file != "." && $file != "..")
					{
						closedir($handle);
						return false;
					}
				}
				closedir($handle);
			}
		}
		return true;
	}

	// publically availably on php.net, posted by Meaulnes Legler
	public static function ModeOctal2rwx($ModeOctal) { // enter octal mode, e.g. '644' or '2755'
		if ( ! preg_match("/[0-7]{3,4}/", $ModeOctal) )    // either 3 or 4 digits
			die("wrong octal mode in ModeOctal2rwx('<TT>$ModeOctal</TT>')");
		$Moctal = ((strlen($ModeOctal)==3)?"0":"").$ModeOctal;    // assume default 0
		$Mode3 = substr($Moctal,-3);    // trailing 3 digits, no sticky bits considered
		$RWX = array ('---','--x','-w-','-wx','r--','r-x','rw-','rwx');    // dumb,huh?
		$Mrwx = $RWX[$Mode3[0]].$RWX[$Mode3[1]].$RWX[$Mode3[2]];    // concatenate
		if (preg_match("/[1357]/", $Moctal[0])) $Mrwx[8] = ($Mrwx[8]=="-")?"T":"t";
		if (preg_match("/[2367]/", $Moctal[0])) $Mrwx[5] = ($Mrwx[5]=="-")?"S":"s";
		if (preg_match("/[4567]/", $Moctal[0])) $Mrwx[2] = ($Mrwx[2]=="-")?"S":"s";
		return $Mrwx;    // returns e.g. 'rw-r--r--' or 'rwxr-sr-x'
	}

	// publically availably on php.net, posted by Meaulnes Legler
	public static function ModeRWX2Octal($Mode_rwx) {    // enter rwx mode, e.g. 'drwxr-sr-x'
		if ( ! preg_match("/[-d]?([-r][-w][-xsS]){2}[-r][-w][-xtT]/", $Mode_rwx) )
			die("wrong <TT>-rwx</TT> mode in ModeRWX2Octal('<TT>$Mode_rwx</TT>')");
		$Mrwx = substr($Mode_rwx, -9);    // 9 chars from the right-hand side
		$ModeDecStr     = (preg_match("/[sS]/",$Mrwx[2]))?4:0;    // pick out sticky
		$ModeDecStr    .= (preg_match("/[sS]/",$Mrwx[5]))?2:0;    // _ bits and change
		$ModeDecStr    .= (preg_match("/[tT]/",$Mrwx[8]))?1:0;    // _ to e.g. '020'
		$Moctal     = $ModeDecStr[0]+$ModeDecStr[1]+$ModeDecStr[2];    // add them
		$Mrwx = str_replace(array('s','t'), "x", $Mrwx);    // change execute bit
		$Mrwx = str_replace(array('S','T'), "-", $Mrwx);    // _ to on or off
		$trans = array('-'=>'0','r'=>'4','w'=>'2','x'=>'1');    // prepare for strtr
		$ModeDecStr    .= strtr($Mrwx,$trans);    // translate to e.g. '020421401401'
		$Moctal    .= $ModeDecStr[3]+$ModeDecStr[4]+$ModeDecStr[5];    // continue
		$Moctal    .= $ModeDecStr[6]+$ModeDecStr[7]+$ModeDecStr[8];    // _ adding
		$Moctal    .= $ModeDecStr[9]+$ModeDecStr[10]+$ModeDecStr[11];  // _ triplets
		return $Moctal;    // returns octal mode, e.g. '2755' from above.
	}

	public static function move_file($from_file, $to_file)
	{
		debug::string("move_file()");
		debug::variable($from_file, 'from_file');
		debug::variable($to_file, 'to_file');

		if (file_exists($from_file))
		{
			if (file_exists($to_file))
			{
				chmod($to_file, 0777);
				unlink($to_file);
			}
			$dir = dirname($to_file);
			debug::variable($dir, 'dir');

			if (!is_dir($dir))
			{
				$is_dir = self::create_dir($dir, 0777);
				debug::variable($is_dir, 'is_dir');
			}
			rename($from_file, $to_file);
		}
		if (file_exists($to_file))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function no_leading_slash($path)
	{
		if (Str::right($path, 1) == "/")
		{
			$path = Str::right($path, strlen($path) - 1);
		}
		return $path;
	}


	public static function no_trailing_slash($path)
	{
		if (Str::left($path, 1) == "/")
		{
			$path = Str::left($path, strlen($path) - 1);
		}
		return $path;
	}

	public static function parent_dir($path, $levels=1)
	{
		// keep the levels a sane number!
		if ($levels < 1)
		{
			$levels = 1;
		}
		if ($levels > 32)
		{
			$levels = 32;
		}

		// remove trailing slash, if any
		if (Str::right($path, 1) == '/')
		{
			$path = Str::left($path, strlen($path)-1);
		}

		// make sure path has leading slash
		if (Str::left($path, 1) <> '/')
		{
			$path = '/'.$path;
		}

		// break path into an array
		$path_array = explode('/', $path);

		// make sure that levels isn't greater then the number of folders deep
		if ($level > count($path_array))
		{
			$levels = count($path_array);
		}

		$new_path = '/';
		for ($i=1; $i < count($path_array) - $levels; $i++)
		{
			$new_path .= $path_array[$i].'/';
		}

		// strip trailing slash if needed
		if (strlen($new_path) > 1)
		{
			$new_path = Str::left($new_path, strlen($new_path)-1);
		}

		return $new_path;
	}

	public static function recursive_mkdir($make_folder)
	{
		debug::variable($make_folder, 'make_folder');
		$directory_separator = '/';
		$folder = explode($directory_separator, $make_folder);
		debug::variable($folder, 'folder');
		$mkfolder = $directory_separator;
		for ($i=0; isset($folder[$i]); $i++)
		{
			$mkfolder .= $folder[$i];
			debug::variable($mkfolder, 'mkfolder');
			if (!@is_dir($mkfolder))
			{
				debug::string("creating ".$mkfolder);
				mkdir("$mkfolder", 0777);
			}
			if (!empty($folder[$i]))
			{
				$mkfolder .= $directory_separator;
			}
		}
		if (@is_dir($make_folder))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function read_file($file_path)
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

	public static function set_path_relative_to_script($file_path)
	{
		$this_script_path = $_SERVER['SCRIPT_FILENAME'];
		//echo "this_script_path=".$this_script_path."<br>";

		$path_parts = explode('/', dirname($this_script_path));
		$seperator_str = "";
		$relative_path = "";
		foreach($path_parts as $key => $path_part)
		{
			if ($key > 0)
			{
			// debug code for path_parts array
			//echo "path_parts[".$key."] = ".$path_part."<br>";
			$relative_path .= $seperator_str."..";
			$seperator_str = "/";
			}
		}

		$file_path = $relative_path.$file_path;
		//echo "file_path=".$file_path."<br>";

		return $file_path;
	}

	// this is probably always the bad choice.
	// use dirsort() below.
	public static function sort_dir_array($dir_array)
	{
		debug::on(false);
		debug::stack_trace();
		debug::variable($dir_array, 'dir_array');
		if (is_array($dir_array) && count($dir_array) > 1)
		{
			//$dir_array_success = usort($dir_array, array($this, '_dirsort'));
			// Need to suppress warning, "Warning: usort(): Array was modified by the user comparison function in"
			$dir_array_success = @usort($dir_array, array(__CLASS__, '_dirsort'));
			debug::variable($dir_array, 'dir_array');
		}
		debug::str_exit();
		return $dir_array;
	}

	public static function strip_path($strip_path, $full_path)
	{
		$strip_strlen = strlen($strip_path);
		$new_path = substr($full_path, $strip_strlen);
		return $new_path;
	}

	//----------------------------------------------------------------------
	// 	Name:  strip_site_path
	// 	Description:
	//		This function removes the leading document root path from a
	//		file path.
	//	Input:  strFilePath
	//	Output:  strNewFilePath
	//----------------------------------------------------------------------
	public static function strip_site_path($strFilePath)
	{
		$strNewFilePath = str_replace(APP_ROOT_DIR, "", $strFilePath);
		return $strNewFilePath;
	}

	public static function URLFriendlyText($SiteURL)
	{
		$SiteURL = trim($SiteURL);
		$SiteURL = str_replace(" ", "-", $SiteURL);
		$SiteURL = str_replace("_", "-", $SiteURL);
		$SiteURL = str_replace("~", "", $SiteURL);
		$SiteURL = str_replace("`", "", $SiteURL);
		$SiteURL = str_replace("!", "", $SiteURL);
		$SiteURL = str_replace("@", "", $SiteURL);
		$SiteURL = str_replace("#", "", $SiteURL);
		$SiteURL = str_replace("$", "", $SiteURL);
		$SiteURL = str_replace("%", "", $SiteURL);
		$SiteURL = str_replace("^", "", $SiteURL);
		$SiteURL = str_replace("&", "", $SiteURL);
		$SiteURL = str_replace("*", "", $SiteURL);
		$SiteURL = str_replace("(", "", $SiteURL);
		$SiteURL = str_replace(")", "", $SiteURL);
		$SiteURL = str_replace("+", "", $SiteURL);
		$SiteURL = str_replace("=", "", $SiteURL);
		$SiteURL = str_replace("{", "", $SiteURL);
		$SiteURL = str_replace("}", "", $SiteURL);
		$SiteURL = str_replace("[", "", $SiteURL);
		$SiteURL = str_replace("]", "", $SiteURL);
		$SiteURL = str_replace("|", "", $SiteURL);
		$SiteURL = str_replace("\\", "", $SiteURL);
		$SiteURL = str_replace(":", "", $SiteURL);
		$SiteURL = str_replace(";", "", $SiteURL);
		$SiteURL = str_replace("\"", "", $SiteURL);
		$SiteURL = str_replace("'", "", $SiteURL);
		$SiteURL = str_replace("<", "", $SiteURL);
		$SiteURL = str_replace(">", "", $SiteURL);
		$SiteURL = str_replace(",", "", $SiteURL);
		$SiteURL = str_replace(".", "", $SiteURL);
		$SiteURL = str_replace("?", "", $SiteURL);
		$SiteURL = str_replace("/", "", $SiteURL);
		$SiteURL = trim($SiteURL);
		// remove any leading dashes
		while (substr($SiteURL, 0, 1) == "-")
		{
			$SiteURL = substr($SiteURL, 1);
		}
		$SiteURL = rawurlencode($SiteURL);
		$SiteURL = str_replace("%96", "", $SiteURL);
		// remove any double dashes
		while (Str::in_str($SiteURL, '--'))
		{
			$SiteURL = str_replace('--', '-', $SiteURL);
		}
		// remove any trailing dashes
		while (Str::right($SiteURL, 1) == '-')
		{
			$SiteURL = Str::left($SiteURL, strlen($SiteURL)-1);
		}

		return $SiteURL;
	}

	public static function write_file($output, $to_file)
	{
		//----------------------------------
		// WRITE THE FILE
		//----------------------------------
		if (strlen($output) > 0)
		{

			// Make directory if it doesn't exist.
			$dir_name = dirname($to_file);
			if (!@is_dir($dir_name))
			{
				//echo "MKDIR NAME: ".$dir_name."<br>";
				//umask(0000);
				self::create_dir($dir_name, 0777);
			}

			// write the file
			$fd = fopen ($to_file, "w");
			$bytes_written = fwrite($fd, $output);
			fclose ($fd);
		}

		if (file_exists($to_file))
		{
			return true;
		}
		else
		{
			return false;
		}
	}


	// usage:
	// usort($my_array, '_dirsort');
	// usort($my_array, array(__CLASS__, '_dirsort'));
	private function _dirsort($a, $b)
	{
		debug::on(false);
		//debug::variable($a, 'a');
		//debug::variable($b, 'b');
		$a = str_replace('\\','/',$a);
		$b = str_replace('\\','/',$b);
		//debug::variable($a, 'a');
		//debug::variable($b, 'b');

		if ($a == $b)
		{
			$return_value = 0;
			//debug::variable($return_value, 'return_value');
			return $return_value;
		}

		$pathdepthA = substr_count($a,'/');
		$pathdepthB = substr_count($b,'/');

		$last_slash_pos = strrpos($a, "/");
		$dirA = substr($a, 0, $last_slash_pos+1);
		$last_slash_pos = strrpos($b, "/");
		$dirB = substr($b, 0, $last_slash_pos+1);

		if ($pathdepthA == $pathdepthB)
		{
			if ($dirA == $dirB)
			{
				if ($a < $b)
				{
					$return_value = -1;
				}
				else
				{
					$return_value = 1;
				}
			}
			else
			{
				if ($dirA < $dirB)
				{
					$return_value = -1;
				}
				else
				{
					$return_value = 1;
				}
			}
			//debug::variable($return_value, 'return_value');
			return $return_value;
		}

		$temp_dirA = str_replace('/', chr(0), $dirA);
		$temp_dirB = str_replace('/', chr(0), $dirB);
		if ($temp_dirA < $temp_dirB)
		{
			$return_value = -1;
		}
		else
		{
			$return_value = 1;
		}

		//debug::variable($return_value, 'return_value');
		return $return_value;
	}

	public static function empty_dir($dir_path)
	{
		debug::on(false);

		$success = false;

		if (@is_dir($dir_path))
		{
			debug::string("dir_path found");
			$handle = opendir($dir_path);
			if ($handle)
			{
				while (false !== ($item = readdir($handle)))
				{
					if ($item != '.' && $item != '..')
					{
						if (@is_dir($dir_path.'/'.$item))
						{
							self::delete_dir($dir_path.'/'.$item);
						}
						else
						{
							// add support for deleting a read only file on windows
							@chmod($dir_path.'/'.$item, 0777);

							unlink($dir_path.'/'.$item);
						}
					}
				}

				// confirm dir is empty
				$is_file_found = false;
				while (false !== ($item = readdir($handle)))
				{
					if ($item != '.' && $item != '..')
					{
						$is_file_found = true;
					}
				}
				debug::variable($is_file_found);

				closedir($handle);

				if (! $is_file_found)
				{
					$success = true;
					debug::variable($success);
				}
			}
		}
		else
		{
			$success = true;
			debug::variable($success);
		}
		return $success;
	}

	// Note: in order to connect to remote servers...
	// in /etc/passwd, daemon would need to have a home directory of /inetpub
	// in /etc/passwd, daemon would need to have a shell of /bin/bash

	// > su daemon
	// > cd /inetpub
	// > mkdir .ssh
	// > set-permissions

	// Save password
	// > sshcopyid <user>@<server>

	// Update known_hosts
	// > ssh <user>@<server>
	public static function rsync($src_dir, $dest_dir, $options=array(
		'verbose' => true,
		'recursive' => true,
		'preserve_modification_times' => true,
		'copy_symlinks_as_symlinks' => true,
		'dry_run' => false,
		'delete_extra_files_from_dest' => true,
		'exclude' => array(),
	))
	{
		debug::on(false);
		debug::string('rsync()');
		debug::variable($src_dir);
		debug::variable($dest_dir);
		debug::variable($options);

		$command = '/bin/rsync ';
		if ($options['verbose'])
		{
			$command .= '-v ';
		}
		if ($options['recursive'])
		{
			$command .= '-r ';
		}
		if ($options['preserve_modification_times'])
		{
			$command .= '-t ';
		}
		if ($options['copy_symlinks_as_symlinks'])
		{
			$command .= '-l ';
		}
		if ($options['dry_run'])
		{
			$command .= '--dry-run ';
		}
		if ($options['delete_extra_files_from_dest'])
		{
			$command .= '--delete ';
		}
		if (is_array($options['exclude']) && count($options['exclude']) > 1)
		{
			$is_first = true;
			$command .= '--exclude={';
			foreach ($options['exclude'] as $exclude)
			{
				if ($is_first)
				{
					$command .= $exclude;
				}
				else
				{
					$command .= ','.$exclude;
				}
				$is_first = false;
			}
			$command .= '} ';
		}
		$command .= $src_dir.' ';
		$command .= $dest_dir.' ';
		debug::variable($command);

		unset($output);
		$result = exec($command, $output, $return_var);
		debug::variable($result);
		debug::variable($output);
		debug::variable($return_var);

		return $output;
	}

}

?>
