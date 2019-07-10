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


namespace Facade;

use debug;
use Facade\Config;
use Facade\Str;

class File
{
	public function _construct()
	{
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

}

?>
