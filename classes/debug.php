<?php
//===================================================================
// z9Debug
//===================================================================
// debug.php
// --------------------
// The set of debug functions to make php coding easier.
//
//       Date Created: 2005-04-23
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================



//------------------------------------------------------------
// 2014-12-02, Allan Vest, Z9 Digital
// converted to class
//------------------------------------------------------------

class debug
{
	private static $last_calling_file = NULL;
	private static $last_calling_line = NULL;
	private static $last_calling_function = NULL;
	private static $last_calling_class = NULL;
	private static $last_calling_class_type = NULL;

	private static $calling_file = NULL;
	private static $calling_line = NULL;
	private static $calling_function = NULL;
	private static $calling_class = NULL;
	private static $calling_class_type = NULL;

	public static $variable_name = '';

	// TODO - remove all references to $show_color
	public static $show_color = '';

	// TODO - remove all references to $log_to_file
	public static $log_to_file = '';

	// TODO - remove all references to $log_to_screen
	public static $log_to_screen = '';

	// TODO - remove all references to $log_file
	public static $log_file = '';

	public static $backtrace = NULL;

	// $output = array(
	//	'calling_file' =>
	//	'calling_line' =>
	//	'calling_function' =>
	//	'calling_class' =>
	//	'calling_class_type' =>
	//	'lines' => array(
	//		'display' => string || variable || method
	//		'name' => variable name
	//		'type' => variable type
	//		'value' => variable value
	//	)
	public static $output = array();

	// $output_buffer = array of 1 or more output's
	// $output_buffer = array(
	//	0 => $output,
	//	1 => $output,
	// )
	public static $output_buffer = array();

	// count total lines in output_buffer
	public static $output_buffer_lines = 0;

	// track length of strings that are going to get added to the output_buffer
	public static $output_buffer_total_len = 0;

	// curr output buffer file index
	public static $output_buffer_page_count = 0;


	// how many times has a variable been displayed
	// used with array('limit'=>1) option
	private static $var_count = array();

	// what is the current number of times the
	// debug::variable has been called in total
	private static $var_count_total = array();

	public static $start_page_load_time = '';
	public static $end_page_load_time = '';
	public static $page_load_time = '';
	public static $page_peak_memory = '';
	public static $page_sql_time = 0;

	// on/off settings
	public static $enabled = array();
	public static $enabled_file = '';
	public static $enabled_function = '';

	public static $timers = array();
	public static $sql_timers = array();

	// $data['supress_output']
	// $data['session_id'] - session_id is assigned for the browser session
	// $data['request_id'] - request_id is per request
	// $data['enabled']
	// $data['default_limit']
	// $data['max_single_level_array_keys']
	// $data['output_buffer_total_len_limit']
	// $data['output_buffer_per_line_len']
	public static $data = array();

	public static $request_full_url = '';
	public static $request_url_path = '';
	public static $request_url_query = '';
	public static $request_date = '';
	public static $web_root = '';
	public static $session_name = '';

	public static $cms_user = array();
	public static $cms_page = array();

	//public static $debug_variables = array();
	public static $php_code_files = array();

	public static $functions_executed = array();

//-----------------------------------------------
// load methods
//-----------------------------------------------

	public static function construct()
	{
		self::$data['enabled'] = true;
		self::$web_root = self::remove_leading(str_replace("\\", "/", Z9DEBUG_DIR), str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']));

		self::$request_date = time();
		self::$request_full_url = '';
		if (isset($_SERVER['REQUEST_URI']))
		{
			self::$request_full_url = $_SERVER['REQUEST_URI'];
		}

		$url_parts = parse_url(self::$request_full_url);
		if (isset($url_parts['query']))
		{
			self::$request_url_query = $url_parts['query'];
		}
		if (isset($url_parts['path']))
		{
			self::$request_url_path = $url_parts['path'];
		}

		if (isset($_COOKIE['z9dsid']))
		{
			self::$data['session_id'] = $_COOKIE['z9dsid'];
		}
		elseif (isset($_GET['z9dsid']))
		{
			self::$data['session_id'] = $_GET['z9dsid'];
		}
		else
		{
			self::$data['session_id'] = uniqid('', true);

			if (php_sapi_name() == 'cli')
			{
				// do nothing
			}
			else
			{
				if (self::get('force_http'))
				{
					setcookie('z9dsid', self::$data['session_id'], null, '/', null, false);
				}
				else
				{
					setcookie('z9dsid', self::$data['session_id'], null, '/', null, true);
				}

				$_COOKIE['z9sid'] = self::$data['session_id'];
			}
		}

		self::$data['request_id'] = uniqid('', true);

		// max number of times to execute a particular debug::variable() call in the code (loop limit)
		self::$data['default_limit'] = 10;

		self::$data['output_buffer_total_len_limit'] = 256 * 1024; // 256K
		// NOTE: the 256K setting results in approx 1MB per line when written to the var_data.txt file as serialized data
		// NOTE: the 256K setting results in a max memory usage of approx 15MB
		// NOTE: setting output_buffer_total_len_limit high will use more memory
		// NOTE: setting output_buffer_total_len_limit low will use less memory but increase the number of writes needed
		//self::$data['output_buffer_total_len_limit'] = 999999 * 1024;
		//self::$data['output_buffer_total_len_limit'] = 1 * 1024;

		// average number of characters of output for each line of debug as overhead not count the value...
		self::$data['output_buffer_per_line_len'] = 50;

		// max number of array elements to debug for any given single level of an array
		self::$data['max_single_level_array_keys'] = 500;

		// for arrays, each element of an array may average about 2k of memory usage when processed.
		// so if we want to keep memory to approx 16MB, then allow a max of 8000 array elements at any one time before "paging".
		// in reality, the page will render between 8000 and 8499 because of the implementation and the use of the max_single_level_array_keys value.
		self::$data['max_lines_per_page'] = 8000;

		// only show 8000 lines of any array, this way an array will never span more than two pages
		// in reality, the page will render between 8000 and 8499 because of the implementation and the use of the max_single_level_array_keys value.
		self::$data['max_lines_per_variable'] = 8000;

		// init toggle settings
		self::$data['force_enabled'] = false;
		self::$data['force_suppress_output'] = false;
		self::$data['force_on'] = array();

		self::load_toggle_settings();

		// start page timer
		self::start_page_load_timer();
	}

	// self::$enabled is the list of files/functions that are debug::on().
	// self::data['enabled'] is whether debug is enabled at all or not
	public static function load_toggle_settings()
	{
		//$debug = true;
		if (is_file(Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'toggle_settings.php'))
		{
			include(Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'settings'.DIRECTORY_SEPARATOR.'toggle_settings.php');
		}

		//if ($debug) { echo "force_enabled=<pre>";print_r(self::$data['force_enabled']);echo "</pre><br>"; }
		//if ($debug) { echo "force_suppress_output=<pre>";print_r(self::$data['force_suppress_output']);echo "</pre><br>"; }
		//if ($debug) { echo "force_on=<pre>";print_r(self::$data['force_on']);echo "</pre><br>"; }

		if (self::$data['force_suppress_output'])
		{
			self::set('suppress_output', true);
		}

		if (self::$data['force_enabled'])
		{
			//if ($debug) { echo "force_enabled is setting enabled to true<br>"; }
			self::$data['enabled'] = true;
		}
		//if ($debug) { echo "enabled=<pre>";print_r(self::$data['enabled']);echo "</pre><br>"; }

		if (is_array(self::$data['force_on']))
		{
			foreach (self::$data['force_on'] as $file => $functions)
			{
				//if ($debug) { echo "file=<pre>";print_r($file);echo "</pre><br>"; }
				//if ($debug) { echo "functions=<pre>";print_r($functions);echo "</pre><br>"; }

				if (is_array($functions))
				{
					foreach ($functions as $function)
					{
						self::force_on($file, $function);
					}
				}
			}
		}
	}

	public static function force_on($file, $function)
	{
		$debug = false;
		if (self::$data['force_enabled'] || self::$data['enabled'])
		{
			$function = str_replace('\\', '/', $function);
			$file = str_replace('\\', '/', $file);

			self::$enabled[$file][$function] = true;
			if ($debug) { echo "force enabled[".$file."][".$function."]=<pre>";print_r(self::$enabled[$file][$function]);echo "</pre><br>"; }
		}
	}

	public static function start_page_load_timer()
	{
		self::$start_page_load_time = self::get_micro_time();
		//echo "start_page_load_time=<pre>";print_r(self::$start_page_load_time);echo "</pre><br>";
	}

	public static function set_cms_user($data)
	{
		self::$cms_user = $data;
	}

	public static function set_cms_page($data)
	{
		self::$cms_page = $data;
	}


//-----------------------------------------------
// shutdown methods
//-----------------------------------------------

	public static function stop_page_load_timer()
	{
		self::$end_page_load_time = self::get_micro_time();
		//echo "end_page_load_time=<pre>";print_r(self::$end_page_load_time);echo "</pre><br>";

		$page_load_time = self::display_micro_time_diff(self::$start_page_load_time, self::$end_page_load_time);
		//echo "page_load_time=<pre>";print_r($page_load_time);echo "</pre><br>";

		self::$page_load_time = $page_load_time;
	}

	public static function set_page_peak_memory()
	{
		$page_peak_memory = memory_get_peak_usage();
		//echo "page_peak_memory=<pre>";print_r($page_peak_memory);echo"</pre><br>";

		self::$page_peak_memory = $page_peak_memory;
		//echo "self::page_peak_memory=<pre>";print_r(self::$page_peak_memory);echo"</pre><br>";
	}

	public static function has_output()
	{
		// check for output_buffer and 1 or more "pages" of var data
		if (!empty(self::$output_buffer) || self::$output_buffer_page_count >= 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	//-----------------------------------
	// save data methods
	//-----------------------------------

	public static function save_var_data()
	{
		//$debug = true;

		$session_name = self::get('session_name');
		if (!empty($session_name))
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'_'.$session_name.'/'.self::$data['request_id'].'/var_data.txt';
		}
		else
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'/'.self::$data['request_id'].'/var_data.txt';
		}
		//if ($debug) { echo "file_path="; print_r($file_path); echo "<br>"; }

		if (is_file($file_path))
		{
			// don't save empty "page" if file already exists
			if (!empty(self::$output_buffer))
			{
				//if ($debug) { echo "append file<br>"; }
				self::append_file(serialize(self::$output_buffer).chr(10), $file_path);
				self::$output_buffer_page_count++;
			}
		}
		else
		{
			//if ($debug) { echo "write file<br>"; }
			self::write_file(serialize(self::$output_buffer).chr(10), $file_path);
			self::$output_buffer_page_count++;
		}
	}

	public static function save_page_data()
	{
		//$debug = false;

		$page_data = array();
		$page_data['request_full_url'] = self::$request_full_url;
		$page_data['request_date'] = self::$request_date;
		$page_data['request_url_path'] = self::$request_url_path;
		$page_data['request_url_query'] = self::$request_url_query;
		$page_data['page_load_time'] = self::$page_load_time;
		$page_data['page_peak_memory'] = self::$page_peak_memory;
		$page_data['page_sql_time'] = self::$page_sql_time;
		$page_data['var_data_page_count'] = self::$output_buffer_page_count;

		$session_name = self::get('session_name');
		if (!empty($session_name))
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'_'.$session_name.'/'.self::$data['request_id'].'/page_data.txt';
		}
		else
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'/'.self::$data['request_id'].'/page_data.txt';
		}
		//if ($debug) { echo "file_path="; print_r($file_path); echo "<br>"; }

		self::write_file(serialize($page_data), $file_path);
	}

	// TODO - make timer data separate from var data
	public static function save_timer_data()
	{
		//$debug = false;

		$session_name = self::get('session_name');
		if (!empty($session_name))
		{
		}
		else
		{
		}

	}

	public static function save_sql_data()
	{
		//$debug = false;

		$session_name = self::get('session_name');
		if (!empty($session_name))
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'_'.$session_name.'/'.self::$data['request_id'].'/sql_data.txt';
		}
		else
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'/'.self::$data['request_id'].'/sql_data.txt';
		}
		//if ($debug) { echo "file_path="; print_r($file_path); echo "<br>"; }

		self::write_file(serialize(self::$sql_timers), $file_path);
	}

	// parts of the following function are originally from PHP Quick Profiler
	// https://github.com/wufoo/PHP-Quick-Profiler
	// Created by Ryan Campbell. Designed by Kevin Hale.
	// Copyright (c) 2009 Infinity Box Inc.
	public static function save_file_data()
	{
		//$debug = false;

		$files = get_included_files();
		//echo "<pre>".print_r($files,true)."</pre>";exit();

		$file_data = array(
			'list' => array(),
			'totals' => array(
				"count" => count($files),
				"size" => 0,
			),
		);

		if (is_array($files))
		{
			foreach($files as $key => $file)
			{
				$executed_file = self::remove_leading($file, $_SERVER['DOCUMENT_ROOT']);
				$functions_executed = (isset(self::$functions_executed[$executed_file])) ? self::$functions_executed[$executed_file] : array();

				$size = @filesize($file);
				$file_data['list'][] = array(
						'name' => self::remove_leading($file, $_SERVER['DOCUMENT_ROOT']),
						'size' => $size,
						'functions_executed' => $functions_executed,
					);
				$file_data['totals']['size'] += $size;
			}
		}
		$file_data['total']['size'] = $file_data['totals']['size'];

		$session_name = self::get('session_name');
		if (!empty($session_name))
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'_'.$session_name.'/'.self::$data['request_id'].'/file_data.txt';
		}
		else
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'/'.self::$data['request_id'].'/file_data.txt';
		}
		//if ($debug) { echo "file_path="; print_r($file_path); echo "<br>"; }

		self::write_file(serialize($file_data), $file_path);

	}

	public static function save_global_data()
	{
		//$debug = false;

		// $_GET, $_POST, $_FILES, $_COOKIE, $_SERVER, $_ENV, $_SESSION
		$get = self::debug_string($_GET, '_GET', '_GET', $index = 0);
		$post = self::debug_string($_POST, '_POST', '_POST', $index = 0);
		$file = self::debug_string($_FILES, '_FILES', '_FILES', $index = 0);
		$cookie = self::debug_string($_COOKIE, '_COOKIE', '_COOKIE', $index = 0);
		$server = self::debug_string($_SERVER, '_SERVER', '_SERVER', $index = 0);
		$env = self::debug_string($_ENV, '_ENV', '_ENV', $index = 0);
		$session = self::debug_string($_SESSION, '_SESSION', '_SESSION', $index = 0);

		//if ($debug) { echo "get=<pre>";print_r($get);echo "</pre><br>"; }
		//if ($debug) { echo "post=<pre>";print_r($post);echo "</pre><br>"; }
		//if ($debug) { echo "file=<pre>";print_r($file);echo "</pre><br>"; }
		//if ($debug) { echo "cookie=<pre>";print_r($cookie);echo "</pre><br>"; }
		//if ($debug) { echo "server=<pre>";print_r($server);echo "</pre><br>"; }
		//if ($debug) { echo "env=<pre>";print_r($env);echo "</pre><br>"; }
		//if ($debug) { echo "session=<pre>";print_r($session);echo "</pre><br>"; }

		$lines = array_merge($get, $post, $file, $cookie, $server, $env, $session );
		//if ($debug) { echo "lines="; print_r($lines); echo "<br>"; }

		//[94] => Array
		//   (
		//	  [display] => variable
		//	  [name] => _SERVER[HTTP_ACCEPT_ENCODING]
		//	  [type] => string
		//	  [value] => Array
		//		 (
		//			[0] => gzip, deflate
		//		 )
		//
		//	  [len] => 13
		//   )

		$session_name = self::get('session_name');
		if (!empty($session_name))
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'_'.$session_name.'/'.self::$data['request_id'].'/request_data.txt';
		}
		else
		{
			$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'/'.self::$data['request_id'].'/request_data.txt';
		}

		//if ($debug) { echo "file_path="; print_r($file_path); echo "<br>"; }

		self::write_file(serialize($lines), $file_path);
	}

	public static function save_cms_data()
	{
		//$debug = false;

		if (is_array(self::$cms_user) && !empty(self::$cms_user) &&
			is_array(self::$cms_page) && !empty(self::$cms_page))
		{
			$cms_user = self::debug_string(self::$cms_user, 'this->user->data', 'this->user->data', $index = 0);
			$cms_page = self::debug_string(self::$cms_page, 'this->page->data', 'this->page->data', $index = 0);

			$lines = array_merge($cms_user, $cms_page);
			//if ($debug) { echo "lines="; print_r($lines); echo "<br>"; }

			//[94] => Array
			//   (
			//	  [display] => variable
			//	  [name] => _SERVER[HTTP_ACCEPT_ENCODING]
			//	  [type] => string
			//	  [value] => Array
			//		 (
			//			[0] => gzip, deflate
			//		 )
			//
			//	  [len] => 13
			//   )

			$session_name = self::get('session_name');
			if (!empty($session_name))
			{
				$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'_'.$session_name.'/'.self::$data['request_id'].'/cms_data.txt';
			}
			else
			{
				$file_path = Z9DEBUG_DIR.'/sessions/'.self::$data['session_id'].'/'.self::$data['request_id'].'/cms_data.txt';
			}

			//if ($debug) { echo "file_path="; print_r($file_path); echo "<br>"; }

			self::write_file(serialize($lines), $file_path);
		}
	}

	public static function save_php_code_files()
	{
		//$start_time = self::get_micro_time();
		if (is_array(self::$php_code_files))
		{
			foreach (self::$php_code_files as $file_path => $dummy)
			{
				//echo "file_path=<pre>";print_r($file_path);echo "</pre><br>";

				$web_file_path = self::remove_leading($file_path, $_SERVER['DOCUMENT_ROOT']);
				//echo "web_file_path=<pre>";print_r($web_file_path);echo "</pre><br>";

				$session_name = self::get('session_name');

				if (!empty($session_name))
				{
					$session_file_path = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.self::$data['session_id'].'_'.$session_name.DIRECTORY_SEPARATOR.self::$data['request_id'].DIRECTORY_SEPARATOR.'files'.$web_file_path;
				}
				else
				{
					$session_file_path = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.self::$data['session_id'].'/'.self::$data['request_id'].DIRECTORY_SEPARATOR.'files'.$web_file_path;
				}
				//echo "session_file_path=<pre>";print_r($session_file_path);echo "</pre><br>";

				if (!is_file($session_file_path))
				{
					if (is_file($file_path))
					{
						// copy file from $file_path to $session_file_path
						self::copy_file($file_path, $session_file_path);
					}
				}
			}
		}

		//$end_time = self::get_micro_time();

		// total time is .0005 to .0006 per file copied... (for a 50 line file)
		// odds are high that only a few files need to be copied
		// even 20 files would only be .01 seconds
		// so it would take 200 files to be .1 seconds
		//$total_time = self::display_micro_time_diff($start_time, $end_time);
		//echo "total_time=<pre>";print_r($total_time);echo "</pre><br>";
	}

	//-----------------------------------------------
	// display methods
	//-----------------------------------------------

	public static function display_console_link()
	{
		$web_root = str_replace("\\", "/", self::$web_root);
		$session_id = self::$data['session_id'];
		$session_name = self::get('session_name');
		if (!empty($session_name))
		{
			$session_id .= '_'.$session_name;
		}
		$request_id = self::$data['request_id'];
$content = <<<CONTENT
<style>
#z9show {
	display:block;
	/*background-color:#f0f0f0;*/
	position:fixed;
	top:0px;
	right:0px;
	/*width:50px;*/
	height:12px;
	z-index:1999999;
	padding-top:0px;
	padding-bottom:5px;
	padding-left:0px;
	padding-right:0px;
	color:#555555;
	font-family:Aria,sans-serif;
	font-size:12px;
	/*border:1px solid #d0d0d0;*/
	cursor:pointer;
	box-sizing: initial;
}
.z9link_nodecor, A.z9link_nodecor:ACTIVE, A.z9link_nodecor:LINK, A.z9link_nodecor:VISITED {
	text-decoration:none;
	font-family:Arial, sans-serif;
	font-size:12px;
	color:blue;
	font-weight:normal;
}
A.z9link_nodecor:HOVER, A.z9link_nodecor:VISITED:HOVER {
	text-decoration:none;
	font-family:Arial, sans-serif;
	font-size:12px;
	color:blue;
	font-weight:normal;
}
.play_btn {
border: 1px solid #777777;
display:inline;
background-color:#888888;
color:white;
padding-left:3px;
padding-right:2px;
padding-top:1px;
padding-bottom:1px;
}
.close_btn {
border: 1px solid #777777;
display:inline;
background-color:#888888;
color:white;
padding-left:4px;
padding-right:4px;
padding-top:1px;
padding-bottom:1px;
}

</style>
<script type="text/javascript">
function z9debug_show()
{
	var my_window = window.open('{$web_root}/console/?z9dsid={$session_id}&z9drid={$request_id}', 'debugconsole');
	my_window.focus();
}
function z9debug_hide()
{
	document.getElementById('z9show').style.display = 'none';
}
</script>
<div id=z9show>
	<a class=z9link_nodecor onclick="z9debug_show();"><div class=play_btn>&#9654;</div></a>
	<a class=z9link_nodecor onclick="z9debug_hide();"><div class=close_btn>&#215;</div></a>
</div>
CONTENT;
		echo $content;
	}

//-----------------------------------------------
// public methods
//-----------------------------------------------

	// enable debug output for a file, function, or class method
	//public static function on($file=null, $function=null)
	public static function on($is_turn_on=true)
	{
		$debug = false;
		if (self::$data['enabled'])
		{
			self::$backtrace = debug_backtrace();
			//if ($debug) { echo "backtrace=<pre>";print_r(self::$backtrace);echo "</pre><br>"; }

			self::set_calling_properties();
			//if ($debug) { echo "calling_file=<pre>";print_r(self::$calling_file);echo "</pre><br>"; }

			// SETS...
			//self::$calling_class
			//self::$calling_class_type
			//self::$calling_line
			//self::$calling_function
			//self::$calling_file

			//----------------------------------------------
			// TODO - record all debug::on() calls
			//----------------------------------------------
			// Record all debug::on() calls to build list of functions called during code execution.
			$executed_function = (!empty(self::$calling_function)) ? self::$calling_function : '-';
			//if ($debug) { echo "executed_function=<pre>";print_r($executed_function);echo "</pre><br>"; }

			$count = (isset(self::$functions_executed[self::$calling_file][$executed_function]['count'])) ? self::$functions_executed[self::$calling_file][$executed_function]['count'] + 1 : 1;
			//if ($debug) { echo "count=<pre>";print_r($count);echo "</pre><br>"; }

			self::$functions_executed[self::$calling_file][$executed_function] = array(
				'file' => self::$calling_file,
				'function' => self::$calling_function,
				'class' => self::$calling_class,
				'class_type' => self::$calling_class_type,
				'line' => self::$calling_line,
				'count' => $count,
			);
		}
		//echo "functions_executed=<pre>";print_r(self::$functions_executed);echo "</pre><br>";

		//echo "enabled=<pre>";print_r(self::$enabled);echo "</pre><br>";
		//if ($is_turn_on && self::$data['enabled'])
		if ($is_turn_on)
		{
			self::set_enabled_location();
			//if ($debug) { echo "enabled_file=<pre>";print_r(self::$enabled_file);echo "</pre><br>"; }
			//if ($debug) { echo "enabled_function=<pre>";print_r(self::$enabled_function);echo "</pre><br>"; }

			//$file = self::$enabled_file;
			$file = self::$enabled_file;
			//	if (empty($file))
			//	{
			//		$file = '-';
			//	}
			$file = str_replace('\\', '/', $file);
			if ($debug) { echo "file=<pre>";print_r($file);echo "</pre><br>"; }

			//$function = self::$enabled_function;
			$function = self::$enabled_function;
			if ( $function == 'include' ||
				$function == 'include_once' ||
				$function == 'require' ||
				$function == 'require_once'
			)
			{
				$function = '-';
			}
			//	if (empty($function))
			//	{
			//		$function = '-';
			//	}
			$function = str_replace('\\', '/', $function);
			if ($debug) { echo "function=<pre>";print_r($function);echo "</pre><br>"; }

			self::$enabled[$file][$function] = true;
			if ($debug) { echo "enabled[".$file."][".$function."]=<pre>";print_r(self::$enabled[$file][$function]);echo "</pre><br>"; }
		}
	}

	// disable debug output for a file, function, or class method
	//public static function off($file=null, $function=null)
	public static function off($is_turn_off=true)
	{
		if ($is_turn_off && self::$data['enabled'] && !empty(self::$enabled))
		{
			//$debug = false;

			//if (empty($file) && empty($function))
			//{
				self::$backtrace = debug_backtrace();
				//if ($debug) { echo "backtrace=<pre>";print_r(self::$backtrace);echo "</pre><br>"; }

				self::set_calling_properties();
				//if ($debug) { echo "calling_file=<pre>";print_r(self::$calling_file);echo "</pre><br>"; }

				self::set_enabled_location();
				//if ($debug) { echo "enabled_file=<pre>";print_r(self::$enabled_file);echo "</pre><br>"; }
				//if ($debug) { echo "enabled_function=<pre>";print_r(self::$enabled_function);echo "</pre><br>"; }

				$file = self::$enabled_file;
				$function = self::$enabled_function;
				if ( $function == 'include' ||
					$function == 'include_once' ||
					$function == 'require' ||
					$function == 'require_once'
				)
				{
					$function = '-';
				}

				$file = str_replace('\\', '/', $file);
				$function = str_replace('\\', '/', $function);

				self::$enabled[$file][$function] = false;
				//if ($debug) { echo "enabled1[".$file."][".$function."]=<pre>";print_r(self::$enabled);echo "</pre><br>"; }
			//}
			//else
			//{
			//	if (empty($file))
			//	{
			//		$file = '-';
			//	}
			//	if (empty($function))
			//	{
			//		$function = '-';
			//	}
			//	$file = str_replace('\\', '/', $file);
			//	$function = str_replace('\\', '/', $function);
			//	self::$enabled[$file][$function] = false;
			//	//if ($debug) { echo "enabled2[".$file."][".$function."]=<pre>";print_r(self::$enabled);echo "</pre><br>"; }
			//}
		}
	}

	public static function variable($variable, $variable_name='', $params=array())
	{
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			$show_color = true;
			$log_to_file = NULL;
			$log_to_screen = true;
			$log_file = NULL;
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				//echo "variable_start=".memory_get_usage()."<br>";
				if (memory_get_usage() <= 134217728)
				{
					if (empty($variable_name))
					{
						// let's delay doing the variable name lookup...
						// the console page can handle this more efficiently
						//$variable_name = self::get_variable_name($backtrace);
						$variable_name = '###EMPTY###';

						self::save_php_file_to_list($backtrace);
					}

					// set default limit
					if (!isset($params['limit']))
					{
						$params['limit'] = self::$data['default_limit'];
					}

					self::debug_var($variable, $variable_name, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace, $params);
				}
				else
				{
					//echo "MEMORY"; exit();
				}
				//echo "variable_end=".memory_get_usage()."<br>";
			}
		}
	}

	public static function constant($variable, $variable_name='', $params=array())
	{
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			$show_color = true;
			$log_to_file = NULL;
			$log_to_screen = true;
			$log_file = NULL;
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				if (empty($variable_name))
				{
					//$variable_name = self::get_variable_name($backtrace);
					$variable_name = '';

					self::save_php_file_to_list($backtrace);
				}

				$result = self::debug_var($variable, $variable_name, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace, $params);
				echo $result;
			}
		}
	}

	public static function string($string, $params=array())
	{
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			$show_color = true;
			$log_to_file = NULL;
			$log_to_screen = true;
			$log_file = NULL;
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				// set default limit
				if (!isset($params['limit']))
				{
					$params['limit'] = self::$data['default_limit'];
				}

				$result = self::debug_str($string, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace, $params);
				echo $result;
			}
		}
	}

	public static function memory($params=array())
	{
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			$show_color = true;
			$log_to_file = NULL;
			$log_to_screen = true;
			$log_file = NULL;
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				$string = "Memory: ".number_format((memory_get_peak_usage()/1024/1024),2,'.',',')." MB";
				$result = self::debug_str($string, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace, $params);
				echo $result;
			}
		}
	}

	public static function is_on($data=array())
	{
		//$debug = false;
		//if (isset(self::$data['app_debug']) && self::$data['app_debug']) { $debug = true; }
		$return = false;

		if (!self::$data['enabled'])
		{
			return false;
		}

		if (!empty(self::$enabled))
		{
			// we only need to figure out the backtrace if called externally
			if (!isset($data['internal_call']) || !$data['internal_call'])
			{
				self::$backtrace = debug_backtrace();
				//if ($debug) { echo "backtrace=<pre>";print_r(self::$backtrace);echo "</pre><br>"; }

				self::set_calling_properties();
				//if ($debug) { echo "calling_file=<pre>";print_r(self::$calling_file);echo "</pre><br>"; }
				//if ($debug) { echo "calling_function=<pre>";print_r(self::$calling_function);echo "</pre><br>"; }
			}

			self::set_enabled_location();
			//if ($debug) { echo "enabled_file=<pre>";print_r(self::$enabled_file);echo "</pre><br>"; }
			//if ($debug) { echo "enabled_function=<pre>";print_r(self::$enabled_function);echo "</pre><br>"; }
			//if ($debug) { echo "enabled=<pre>";print_r(self::$enabled);echo "</pre><br>"; }


			$enabled_file = self::$enabled_file;
			$enabled_file = str_replace('\\', '/', $enabled_file);
			//if ($debug) { echo "enabled_file=<pre>";print_r($enabled_file);echo "</pre><br>"; }

			$enabled_function = self::$enabled_function;
			$enabled_function = str_replace('\\', '/', $enabled_function);
			if ( $enabled_function == 'include' ||
				$enabled_function == 'include_once' ||
				$enabled_function == 'require' ||
				$enabled_function == 'require_once'
			)
			{
				$enabled_function = '-';
			}
			//if ($debug) { echo "enabled_function=<pre>";print_r($enabled_function);echo "</pre><br>"; }

			$enabled_function2 = '';
			if (self::in_str(self::$enabled_function, '::'))
			{
				list($class_name, $method_name) = explode("::", self::$enabled_function);
				//if ($debug) { echo "class_name=<pre>";print_r($class_name);echo "</pre><br>"; }
				//if ($debug) { echo "method_name=<pre>";print_r($method_name);echo "</pre><br>"; }
				$enabled_function2 = $class_name.'::*';
			}
			if ( $enabled_function2 == 'include' ||
				$enabled_function2 == 'include_once' ||
				$enabled_function2 == 'require' ||
				$enabled_function2 == 'require_once'
			)
			{
				$enabled_function2 = '-';
			}
			//if ($debug) { echo "enabled_function2=<pre>";print_r($enabled_function2);echo "</pre><br>"; }

			//echo "backtrace=<pre>";print_r(self::$backtrace);echo "</pre><br>";
			//if ($debug) { echo "enabled_file=<pre>";print_r($enabled_file);echo "</pre><br>"; }
			//if ($debug) { echo "enabled_function=<pre>";print_r($enabled_function);echo "</pre><br>"; }
			//if ($debug) { echo "enabled_function2=<pre>";print_r($enabled_function2);echo "</pre><br>"; }
			//if ($debug) { echo "enabled=<pre>";print_r(self::$enabled);echo "</pre><br>"; }

			if (isset(self::$enabled[$enabled_file][$enabled_function]) && self::$enabled[$enabled_file][$enabled_function])
			{
				$return = true;
			}
			else
			{
				if ( !empty($enabled_function2) &&
					isset(self::$enabled[$enabled_file][$enabled_function2]) &&
					self::$enabled[$enabled_file][$enabled_function2]
					)
				{
					$return = true;
				}
				else
				{
					$return = false;
				}
			}

		}

		self::set_last_calling_properties();

		//if ($debug) { echo "return=<pre>";print_r($return);echo "</pre><br>"; }
		return $return;
	}

	public static function sql_start($sql, $backtrace)
	{
		if (self::$data['enabled'])
		{
			//$debug = false;

			$sql_id = '';
			if (!self::get('disable_sql_recording'))
			{

				// record all sql queries regardless of self::$enabled and self::is_on().
				self::$backtrace = $backtrace;
				if (	self::$backtrace[1]['function'] == 'build_array_from_sql' ||
					self::$backtrace[1]['function'] == 'get_single_value_from_sql' ||
					self::$backtrace[1]['function'] == 'get_single_row_from_sql')
				{
					self::pop_backtrace_level();
				}
				//if ($debug) { echo "backtrace=<pre>";print_r($backtrace);echo"</pre><br>"; }
				self::set_calling_properties();

				//if ($debug) { echo "sql=<pre>";print_r($sql);echo"</pre><br>"; }
				//if ($debug) { echo "calling_file=".self::$calling_file."<br>"; }
				//if ($debug) { echo "calling_line=".self::$calling_line."<br>"; }
				//if ($debug) { echo "calling_function=".self::$calling_function."<br>"; }
				//if ($debug) { echo "calling_class=".self::$calling_class."<br>"; }
				//if ($debug) { echo "calling_class_type=".self::$calling_class_type."<br>"; }


				$sql_id = count(self::$sql_timers);
				//if ($debug) { echo "sql_id=<pre>";print_r($sql_id);echo"</pre><br>"; }

				self::$sql_timers[$sql_id]['sql'] = $sql;
				self::$sql_timers[$sql_id]['start'] = self::get_micro_time();

				self::$sql_timers[$sql_id]['from_file'] = self::$calling_file;
				self::$sql_timers[$sql_id]['from_line'] = self::$calling_line;
				self::$sql_timers[$sql_id]['from_function'] = self::$calling_function;
				self::$sql_timers[$sql_id]['from_class'] = self::$calling_class;

				//if ($debug) { echo "sql_timer=<pre>";print_r(self::$sql_timers[$sql_id]);echo"</pre><br>"; }

			}
			return $sql_id;
		}
	}

	public static function sql_end($sql_id)
	{
		if (self::$data['enabled'])
		{
			//$debug = true;
			//if ($debug) { echo "sql_id=<pre>";print_r($sql_id);echo"</pre><br>"; }
			if (!self::get('disable_sql_recording'))
			{

				if (isset(self::$sql_timers[$sql_id]))
				{
					self::$sql_timers[$sql_id]['end'] = self::get_micro_time();
					self::$sql_timers[$sql_id]['total'] = self::display_micro_time_diff(self::$sql_timers[$sql_id]['start'], self::$sql_timers[$sql_id]['end']);
					//if ($debug) { echo "sql_timer=<pre>";print_r(self::$sql_timers[$sql_id]);echo"</pre><br>"; }

					self::$page_sql_time += self::$sql_timers[$sql_id]['total'];

					return self::$sql_timers[$sql_id]['total'];
				}
			}
			return 0;
		}
	}

	// input: timer_name
	// output: timer_id
	public static function timer_start($timer_name)
	{
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				//$debug = false;
				//if ($debug) { echo "timer_name=<pre>";print_r($timer_name);echo"</pre><br>"; }

				$timer_id = count(self::$timers);
				//if ($debug) { echo "timer_id=<pre>";print_r($timer_id);echo"</pre><br>"; }

				self::$timers[$timer_id]['name'] = $timer_name;
				self::$timers[$timer_id]['start'] = self::get_micro_time();
				//if ($debug) { echo "timer=<pre>";print_r(self::$timers[$timer_id]);echo"</pre><br>"; }

				return $timer_id;
			}
		}
	}

	// input: timer_id
	// output: time
	public static function timer_end($timer_id)
	{
		//echo "timer_end<br>";
		//echo "timer_id=<pre>";print_r($timer_id);echo"</pre><br>";
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			//echo "debug enabled<br>";
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				//echo "is on<br>";
				//echo "timers=<pre>";print_r(self::$timers);echo"</pre><br>";
				if (isset(self::$timers[$timer_id]))
				{
					//echo "timer_id set<br>";
					//$debug = false;
					//if ($debug) { echo "timer_id=<pre>";print_r($timer_id);echo"</pre><br>"; }

					self::$show_color = true;
					self::$log_to_file = false;
					self::$log_to_screen = true;

					self::$timers[$timer_id]['end'] = self::get_micro_time();
					self::$timers[$timer_id]['total'] = self::display_micro_time_diff(self::$timers[$timer_id]['start'], self::$timers[$timer_id]['end']);
					//if ($debug) { echo "timer=<pre>";print_r(self::$timers[$timer_id]);echo"</pre><br>"; }

					$string = 'TIMER: '.self::$timers[$timer_id]['name'].': '.self::$timers[$timer_id]['total'];
					//if ($debug) { echo "string=<pre>";print_r($string);echo"</pre><br>"; }

					$string_len = strlen($string);
					self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

					// display string
					self::$output = array();
					self::set_output_calling_properties();
					$string_lines = explode("\n", $string);
					if (is_array($string_lines))
					{
						foreach ($string_lines as $key => $string_line)
						{
							$string_lines[$key] = str_replace("\r", "", $string_line);
						}
					}
					//if ($debug) { echo "string_lines=";print_r($string_lines); echo "<br>"; }
					self::$output['lines'][0] = array(
						'display' => 'string',
						'name' => '',
						'type' => '',
						'value' => $string_lines,
						'len' => $string_len,
					);
					self::add_output_to_buffer();

					self::set_last_calling_properties();

					return self::$timers[$timer_id]['total'];
				}
			}
		}
	}

	// http://stackoverflow.com/questions/4282120/is-there-a-pretty-print-stack-dump
	public static function stack_trace()
	{
		if (self::$data['enabled'])
		{
			$stack = debug_backtrace();
			$output = '';

			// reverse the order of the stack
			$stack = array_reverse($stack);

			$stackLen = count($stack);

			for ($i = 0; $i < $stackLen-1; $i++)
			{
				$entry = $stack[$i];

				$func = $entry['function'] . '(';
				$argsLen = count($entry['args']);
				for ($j = 0; $j < $argsLen; $j++)
				{
					$my_entry = $entry['args'][$j];
					if (is_string($my_entry))
					{
						$func .= $my_entry;
					}
					if ($j < $argsLen - 1)
					{
						$func .= ', ';
					}
				}
				$func .= ')';

				$entry_file = 'NO_FILE';
				if (array_key_exists('file', $entry))
				{
					$entry_file = $entry['file'];
				}
				$entry_line = 'NO_LINE';
				if (array_key_exists('line', $entry))
				{
					$entry_line = $entry['line'];
				}
				$output .= '#'.($i+1).' '.$entry_file . ':' . $entry_line . ' => ' . $func . PHP_EOL;
			}
			//print_r($output); exit();
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();
			$result = self::debug_str($output, true, NULL, true, NULL, $backtrace, array());
		}
	}

	public static function str_exit($string='', $params=array())
	{
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			$show_color = true;
			$log_to_file = NULL;
			$log_to_screen = true;
			$log_file = NULL;
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				$string = 'EXIT: '.$string;
				$result = self::debug_str($string, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace, $params);
				echo $result;
				exit();
			}
		}
	}

	public static function micro_timestamp($string='')
	{
		$micro_timestamp = microtime();
		list($usec, $sec) = explode(' ', $micro_timestamp);
		$micro_timestamp = (float) $sec + (float) $usec;

		$display_timestamp = self::convert_unix_date($sec, 'yyyy-mm-dd hh:mm:ss');
		$display_timestamp .= self::remove_leading($usec, '0');

		if (!empty($string))
		{
			$string = '['.$display_timestamp.'] '.$string;
		}
		else
		{
			$string = '['.$display_timestamp.']';
		}

		// same as string()
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			$show_color = true;
			$log_to_file = NULL;
			$log_to_screen = true;
			$log_file = NULL;
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				// set default limit
				if (!isset($params['limit']))
				{
					$params['limit'] = self::$data['default_limit'];
				}

				$result = self::debug_str($string, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace, $params);
				echo $result;
			}
		}
	}

	public static function timestamp($string='')
	{
		$timestamp = time();
		$display_timestamp = self::convert_unix_date($timestamp, 'yyyy-mm-dd hh:mm:ss');
		if (!empty($string))
		{
			$string = '['.$display_timestamp.'] '.$string;
		}
		else
		{
			$string = '['.$display_timestamp.']';
		}

		// same as string()
		if (self::$data['enabled'] && !empty(self::$enabled))
		{
			$show_color = true;
			$log_to_file = NULL;
			$log_to_screen = true;
			$log_file = NULL;
			$backtrace = debug_backtrace();
			self::$backtrace = $backtrace;
			self::set_calling_properties();

			if (self::is_on(array('internal_call' => true)))
			{
				// set default limit
				if (!isset($params['limit']))
				{
					$params['limit'] = self::$data['default_limit'];
				}

				$result = self::debug_str($string, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace, $params);
				echo $result;
			}
		}

	}

	// deprecated method
	public static function get_output_data()
	{
		return self::$output_buffer;
	}

	// deprecated method
	public static function add_output_data($data)
	{
		if (!empty($data) && is_array($data))
		{
			self::$output_buffer = array_merge(self::$output_buffer, $data);
		}
	}

//-----------------------------------------------
// settings methods
//-----------------------------------------------

	public static function get($var_name)
	{
		if (isset(self::$data[$var_name]))
		{
			return self::$data[$var_name];
		}
	}

	public static function set($var_name, $var_value)
	{
		// do not allow spaces in session_name
		if ($var_name == 'session_name')
		{
			$var_value = str_replace(' ', '', $var_value);
		}

		//echo "var_name=";print_r($var_name); echo "<br>";
		//echo "var_value=";print_r($var_value); echo "<br>";
		self::$data[$var_name] = $var_value;
		return true;
	}

	public static function enabled($boolean=true)
	{
		$ok_to_set = true;
		if (self::$data['force_enabled'])
		{
			$ok_to_set = false;
		}
		if ($ok_to_set)
		{
			debug::set('enabled', $boolean);
		}
	}

	public static function suppress_output($boolean=true)
	{
		$ok_to_set = true;
		if (self::$data['force_suppress_output'])
		{
			$ok_to_set = false;
		}
		if ($ok_to_set)
		{
			debug::set('suppress_output', $boolean);
		}
	}

	public static function disable_sql_recording($boolean=true)
	{
		debug::set('disable_sql_recording', $boolean);
	}

	public static function session_name($session_name='')
	{
		if (!empty($session_name))
		{
			debug::set('session_name', $session_name);
		}
	}

	public static function default_limit($default_limit=10)
	{
		if (!empty($default_limit))
		{
			debug::set('default_limit', $default_limit);
		}
	}

//-----------------------------------------------
// supporting methods
//-----------------------------------------------

	// used by on(), off(), and is_on()
	private static function set_enabled_location()
	{
		//$debug = false;
		//if ($debug) { echo "backtrace=<pre>";print_r(self::$backtrace);echo "</pre><br>"; }
		//if ($debug) { echo "calling_file=<pre>";print_r(self::$calling_file);echo "</pre><br>"; }
		//if ($debug) { echo "calling_function=<pre>";print_r(self::$calling_function);echo "</pre><br>"; }
		//if ($debug) { echo "calling_class=<pre>";print_r(self::$calling_class);echo "</pre><br>"; }
		//if ($debug) { echo "calling_class_type=<pre>";print_r(self::$calling_class_type);echo "</pre><br>"; }

		self::$enabled_file = '';
		self::$enabled_function = '';

		self::$enabled_file = self::$calling_file;

		if (!empty(self::$calling_function))
		{
			self::$enabled_function = self::$calling_function;
		}
		if (!empty(self::$calling_class))
		{
			self::$enabled_function = self::$calling_class.'::'.self::$calling_function;
		}

		if (empty(self::$enabled_file))
		{
			self::$enabled_file = '-';
		}
		if (empty(self::$enabled_function))
		{
			self::$enabled_function = '-';
		}
		//if ($debug) { echo "enabled_file=<pre>";print_r(self::$enabled_file);echo "</pre><br>"; }
		//if ($debug) { echo "enabled_function=<pre>";print_r(self::$enabled_function);echo "</pre><br>"; }
	}

	// used by sql_start()
	private static function pop_backtrace_level()
	{
		if (is_array(self::$backtrace))
		{
			if (isset(self::$backtrace[0]))
			{
				unset(self::$backtrace[0]);
			}

			$new_backtrace = array();
			foreach (self::$backtrace as $key => $value)
			{
				$new_backtrace[] = $value;
			}
			self::$backtrace = $new_backtrace;
		}
	}

	// used by debug_var() and debug_str()
	private static function increment_var_count()
	{
		if (isset(self::$var_count[self::$calling_file][self::$calling_line]))
		{
			self::$var_count[self::$calling_file][self::$calling_line]++;
		}
		else
		{
			self::$var_count[self::$calling_file][self::$calling_line] = 1;
		}
		return self::$var_count[self::$calling_file][self::$calling_line];
	}

	// used by debug_var()
	private static function increment_var_count_total()
	{
		if (isset(self::$var_count_total[self::$calling_file][self::$calling_line]))
		{
			self::$var_count_total[self::$calling_file][self::$calling_line]++;
		}
		else
		{
			self::$var_count_total[self::$calling_file][self::$calling_line] = 1;
		}
		return self::$var_count_total[self::$calling_file][self::$calling_line];
	}

	// used by debug_var() and debug_str()
	private static function var_count_ok($limit, $start=-1)
	{
		$var_count = 0;
		if (isset(self::$var_count[self::$calling_file][self::$calling_line]))
		{
			$var_count = self::$var_count[self::$calling_file][self::$calling_line];
		}

		$var_count_total = 0;
		if (isset(self::$var_count_total[self::$calling_file][self::$calling_line]))
		{
			$var_count_total = self::$var_count_total[self::$calling_file][self::$calling_line];
		}

		// if start not set
		if ($start == -1)
		{
			if ($var_count < $limit)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ($var_count_total >= ($start - 1) && $var_count_total < $limit + $start - 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	// used by variable() and constant()
	private static function save_php_file_to_list($backtrace)
	{
		$file_path = $backtrace[0]['file'];
		//echo "file_path=<pre>";print_r($file_path);echo "</pre><br>";

		// check if we have created a copy of the file already...
		if (!isset(self::$php_code_files[$file_path]))
		{
			self::$php_code_files[$file_path] = '';
		}
	}

	// use by public methods, debug_var(), and debug_str()
	private static function set_calling_properties()
	{
		//$debug = false;
		//if ($debug) { echo "<hr>"; }
		//if ($debug) { echo "set_calling_properties()<br>"; }
		self::$calling_class = '';
		if (isset(self::$backtrace[1]['class']))
		{
			self::$calling_class = self::$backtrace[1]['class'];
		}
		self::$calling_class_type = '';
		if (isset(self::$backtrace[1]['type']))
		{
			self::$calling_class_type = self::$backtrace[1]['type'];
		}
		self::$calling_file = self::$backtrace[0]['file'];
		self::$calling_file = self::remove_leading(self::$calling_file, $_SERVER['DOCUMENT_ROOT']);
		self::$calling_function = '';
		self::$calling_line = self::$backtrace[0]['line'];
		if (	self::$backtrace[0]['function'] == 'debug_var')
		{
			self::$calling_function = '';
			if (isset(self::$backtrace[1]['function']))
			{
				self::$calling_function = self::$backtrace[1]['function'];
			}
			self::$calling_file = self::$backtrace[0]['file'];
			self::$calling_file = self::remove_leading(self::$calling_file, $_SERVER['DOCUMENT_ROOT']);
		}
		elseif (isset(self::$backtrace[1]['function']))
		{
			self::$calling_function = self::$backtrace[1]['function'];
			self::$calling_file = self::$backtrace[0]['file'];
			self::$calling_file = self::remove_leading(self::$calling_file, $_SERVER['DOCUMENT_ROOT']);
		}
		//if ($debug) { echo "backtrace=<pre>";print_r(self::$backtrace)."</pre><br>"; }
		//if ($debug) { echo "calling_file=".self::$calling_file."<br>"; }
		//if ($debug) { echo "calling_line=".self::$calling_line."<br>"; }
		//if ($debug) { echo "calling_function=".self::$calling_function."<br>"; }
		//if ($debug) { echo "calling_class=".self::$calling_class."<br>"; }
		//if ($debug) { echo "calling_class_type=".self::$calling_class_type."<br>"; }
		//if ($debug) { echo "last_calling_file=".self::$last_calling_file."<br>"; }
		//if ($debug) { echo "last_calling_line=".self::$last_calling_line."<br>"; }
		//if ($debug) { echo "last_calling_function=".self::$last_calling_function."<br>"; }
		//if ($debug) { echo "last_calling_class=".self::$last_calling_class."<br>"; }
		//if ($debug) { echo "last_calling_class_type=".self::$last_calling_class_type."<br>"; }
		//if ($debug) { echo "<hr>"; }
	}

	// used by is_on(), timer_end(), debug_var(), debug_str()
	private static function set_last_calling_properties()
	{
		self::$last_calling_file = self::$calling_file;
		self::$last_calling_line = self::$calling_line;
		self::$last_calling_function = self::$calling_function;
		self::$last_calling_class = self::$calling_class;
		self::$last_calling_class_type = self::$calling_class_type;
	}

	// used by timer_end(), debug_var(), debug_str()
	private static function set_output_calling_properties()
	{
		self::$output['calling_file'] = self::$calling_file;
		self::$output['calling_line'] = self::$calling_line;
		self::$output['calling_function'] = self::$calling_function;
		self::$output['calling_class'] = self::$calling_class;
		self::$output['calling_class_type'] = self::$calling_class_type;
	}

	// used by timer_end(), debug_var(), debug_str()
	private static function add_output_to_buffer()
	{
		//$debug = true;
		//if ($debug) { echo "output="; print_r(self::$output); echo "<br>"; }

		//if ($debug) { echo "output_buffer_total_len="; print_r(self::$output_buffer_total_len); echo "<br>"; }
		//if ($debug) { echo "output_buffer_total_len_limit="; print_r(self::$data['output_buffer_total_len_limit']); echo "<br>"; }

		if (self::$output_buffer_total_len >= self::$data['output_buffer_total_len_limit'])
		{
			// save output_buffer to file...
			//if ($debug) { echo "save_var_data()<br>"; }
			self::$output_buffer[] = self::$output;
			self::save_var_data();

			// reset output_buffer
			self::$output_buffer = array();

			// reset output_buffer_total_len
			self::$output_buffer_total_len = 0;

			// reset output_buffer_lines
			self::$output_buffer_lines = 0;
		}
		else
		{
			self::$output_buffer[] = self::$output;
			//if ($debug) { echo "output_buffer=<pre>"; print_r(self::$output_buffer); echo "</pre><br>"; }

			// each call to debug::variable(), debug::string(), etc will add a new set of lines...
			if (isset(self::$output['lines']))
			{
				self::$output_buffer_lines += count(self::$output['lines']);
			}
		}
	}

//	private static function get_variable_name($backtrace)
//	{
//		$variable_name = '';
//		//echo "backtrace=<pre>";print_r($backtrace);echo "</pre><br>";
//		$file_name = $backtrace[0]['file'];
//		//echo "file_name=<pre>";print_r($file_name);echo "</pre><br>";
//		$file_line_no = $backtrace[0]['line'];
//		//echo "file_line_no=<pre>";print_r($file_line_no);echo "</pre><br>";
//
//		if (false)
//		{
//			// this is the old method
//			$file_contents = self::read_file($file_name);
//			//echo "file_contents=<pre>";print_r($file_contents);echo "</pre><br>";
//			$file_lines = explode("\n", $file_contents);
//			//echo "file_lines=<pre>";print_r($file_lines);echo "</pre><br>";
//			$file_line = '<'.'?php '.$file_lines[$file_line_no-1].' ?'.'>';
//			//echo "file_line=<pre>";print_r($file_line);echo "</pre><br>";
//		}
//		else
//		{
//			// check if we have the value cached already...
//			if (isset(self::$debug_variables[$file_name][$file_line_no-1]))
//			{
//				$file_line = self::$debug_variables[$file_name][$file_line_no-1];
//				$file_line = '<'.'?php '.$file_line.' ?'.'>';
//			}
//			else
//			{
//				// the new method should be faster
//				// this methods adds approximately .00065 seconds per debug:variable call.
//				// so, 1000 debug calls would add .65 seconds.
//				$file = new SplFileObject($file_name);
//				$file->seek($file_line_no-1);
//				$file_line = $file->current();
//				self::$debug_variables[$file_name][$file_line_no-1] = $file_line;
//				$file_line = '<'.'?php '.$file_line.' ?'.'>';
//				//echo "file_line=<pre>";print_r($file_line);echo "</pre><br>";
//			}
//		}
//
//		$tokens = token_get_all($file_line);
//		//echo "tokens=<pre>";print_r($tokens);echo "</pre><br>";
//		if (is_array($tokens))
//		{
//			foreach ($tokens as $token)
//			{
//				if (is_array($token) && isset($token[0]))
//				{
//					//307=T_STRING
//					//368=T_OPEN_TAG
//					//370=T_CLOSE_TAG
//					//371=T_WHITESPACE
//					//376=T_DOUBLE_COLON
//					//309=T_VARIABLE
//					//echo $token[0].'='.token_name($token[0])."<br>";
//					if ($token[0] == T_VARIABLE)
//					{
//						if (empty($variable_name))
//						{
//							$variable_name = self::remove_leading($token[1], '$');
//						}
//					}
//				}
//			}
//		}
//		//echo "variable_name=<pre>";print_r($variable_name);echo"</pre><br>";
//		return $variable_name;
//	}

//-----------------------------------------------
// debug variable methods
//-----------------------------------------------

	//------------------------------------------------------------
	// 2005-04-23, Allan Vest, Z9 Digital
	// debug_var added and ss_as_string reworked
	//------------------------------------------------------------
	public static function debug_var(&$variable, $variable_name="debug", $show_color = true, $log_to_file = false, $log_to_screen = true, $log_file = null, $backtrace = null, $params = array())
	{
		//echo "debug_var_start=".memory_get_usage()."<br>";
		//echo "variable=<pre>"; print_r($variable); echo "</pre><br>";
		//echo "variable_name=<pre>"; print_r($variable_name); echo "</pre><br>";
		//echo "show_color=<pre>"; print_r($show_color); echo "</pre><br>";
		//echo "log_to_file=<pre>"; print_r($log_to_file); echo "</pre><br>";
		//echo "log_to_screen=<pre>"; print_r($log_to_screen); echo "</pre><br>";
		//echo "log_file=<pre>"; print_r($log_file); echo "</pre><br>";
		//echo "backtrace=<pre>"; print_r($backtrace); echo "</pre><br>";

		self::$variable_name = $variable_name;
		self::$show_color = $show_color;
		self::$log_to_file = $log_to_file;
		self::$log_to_screen = $log_to_screen;
		self::$log_file = $log_file;

		if (empty($backtrace))
		{
			self::$backtrace = debug_backtrace();
		}
		else
		{
			self::$backtrace = $backtrace;
		}
		self::set_calling_properties();

		self::$output = array();
		self::set_output_calling_properties();

		$is_limit_set = false;
		$limit = -1;
		if (isset($params['limit']) && $params['limit'] > 0)
		{
			$is_limit_set = true;
			$limit = $params['limit'];
		}
		//echo "is_limit_set=<pre>";print_r($is_limit_set);echo "</pre><br>";
		//echo "limit=<pre>";print_r($limit);echo "</pre><br>";

		$is_start_set = false;
		$start = -1;
		if (isset($params['start']) && $params['start'] > 0)
		{
			$is_start_set = true;
			$start = $params['start'];
		}

		$var_count_ok = true;
		if ($is_limit_set)
		{
			if (!self::var_count_ok($limit, $start))
			{
				$var_count_ok = false;
			}
		}
		//echo "var_count_ok=<pre>";print_r($var_count_ok);echo "</pre><br>";

		if ($var_count_ok)
		{
			if (!isset($variable))
			{
				$string_len = 0;
				self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

				self::$output['lines'][0] = array(
					'display' => 'variable',
					'name' => $variable_name,
					'type' => 'not set',
					'value' => array(
						0 => '',
					),
					//'len' => $string_len,
				);
			}
			else
			{
				self::$output['lines'] = self::debug_string($variable, $variable_name, $variable_name, $index = 0, $params);
			}

			if ($is_limit_set)
			{
				$new_var_count = self::increment_var_count();
				//echo "new_var_count=<pre>";print_r($new_var_count);echo "</pre><br>";
			}

			self::add_output_to_buffer();
		}

		self::set_last_calling_properties();

		$new_var_count_total = self::increment_var_count_total();
		//echo "debug_var_end=".memory_get_usage()."<br>";
	}

	public static function debug_str($string, $show_color = true, $log_to_file = false, $log_to_screen = true, $log_file = null, $backtrace = null, $params = array())
	{
		//$debug = false;
		//if ($debug) { echo "string="; print_r($string); echo "<br>"; }
		//if ($debug) { echo "show_color="; print_r($show_color); echo "<br>"; }
		//if ($debug) { echo "log_to_file="; print_r($log_to_file); echo "<br>"; }
		//if ($debug) { echo "log_to_screen="; print_r($log_to_screen); echo "<br>"; }
		//if ($debug) { echo "log_file="; print_r($log_file); echo "<br>"; }
		//if ($debug) { echo "backtrace="; print_r($backtrace); echo "<br>"; }

		self::$variable_name = '';
		self::$show_color = $show_color;
		self::$log_to_file = $log_to_file;
		self::$log_to_screen = $log_to_screen;
		self::$log_file = $log_file;

		if (empty($backtrace))
		{
			self::$backtrace = debug_backtrace();
		}
		else
		{
			self::$backtrace = $backtrace;
		}
		self::set_calling_properties();

		self::$output = array();
		self::set_output_calling_properties();

		$is_limit_set = false;
		$limit = -1;
		if (isset($params['limit']) && $params['limit'] > 0)
		{
			$is_limit_set = true;
			$limit = $params['limit'];
		}
		//echo "is_limit_set=<pre>";print_r($is_limit_set);echo "</pre><br>";
		//echo "limit=<pre>";print_r($limit);echo "</pre><br>";

		$is_start_set = false;
		$start = -1;
		if (isset($params['start']) && $params['start'] > 0)
		{
			$is_start_set = true;
			$start = $params['start'];
		}

		$var_count_ok = true;
		if ($is_limit_set)
		{
			if (!self::var_count_ok($limit, $start))
			{
				$var_count_ok = false;
			}
		}
		//echo "var_count_ok=<pre>";print_r($var_count_ok);echo "</pre><br>";



		if ($var_count_ok)
		{

			$string_len = strlen($string);
			self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

			$string_lines = explode("\n", $string);
			if (is_array($string_lines))
			{
				foreach ($string_lines as $key => $string_line)
				{
					$string_lines[$key] = str_replace("\r", "", $string_line);
				}
			}
			//if ($debug) { echo "string_lines=";print_r($string_lines); echo "<br>"; }

			self::$output['lines'][0] = array(
				'display' => 'string',
				'name' => '',
				'type' => '',
				'value' => $string_lines,
				'len' => $string_len,
			);

			if ($is_limit_set)
			{
				$new_var_count = self::increment_var_count();
				//echo "new_var_count=<pre>";print_r($new_var_count);echo "</pre><br>";
			}

			self::add_output_to_buffer();
		}

		self::set_last_calling_properties();
	}

	private static function debug_array(&$array, $array_name, $org_array_name='', $index = 0, $params = array())
	{
		//$debug = false;
		//if ($debug) { echo "debug_array()<br>"; }

		$array_row_count = 0;
		$pre_page_line_count = 0;
		$return_array = array();
		$line_cutoff_displayed = false;
		if (is_array($array))
		{
			foreach($array as $var => $val)
			{
				$all_lines = false;
				if (isset($params['all_lines']) && $params['all_lines'])
				{
					$all_lines = true;
				}

				if ($all_lines || $pre_page_line_count + count($return_array) <= self::$data['max_lines_per_variable'])
				{
					if ($array_row_count < self::$data['max_single_level_array_keys'])
					{
						$return2 = self::debug_string($val, $array_name . '[' . $var . ']', $org_array_name, $index+1, $params);

						if (is_array($return2))
						{
							$return_array = array_merge($return_array, $return2);
						}

						// right here, we need to decide if we are going to page...
						$page_output_lines = self::$output_buffer_lines + count($return_array);
						//echo "page_output_lines = ".$page_output_lines."<br>";

						if ($page_output_lines > self::$data['max_lines_per_page'] && !empty($return_array))
						{
							//echo "max array lines memory=".memory_get_usage()."<br>";

							$pre_page_line_count += count($return_array);
							//echo "pre_page_line_count = ".$pre_page_line_count."<br>";
							self::$output['lines'] = &$return_array;
							// force a page when calling add_output_to_buffer()...
							self::$output_buffer_total_len = self::$data['output_buffer_total_len_limit'] + 1;
							self::add_output_to_buffer();
							// clear the return_array value...
							$return_array = array();
						}

					}
					else
					{
						// show array keys cutoff note
						// get the last key
						end($array);
						$key = key($array);
						reset($array);

						$string = self::$data['max_single_level_array_keys'].' of '.count($array).' rows displayed';

						$return3 = self::debug_array_cutoff($string, $array_name.'[...]', $org_array_name, $index+1);
						if (is_array($return3))
						{
							$return_array = array_merge($return_array, $return3);
						}

						break;
					}
					$array_row_count++;
				}
				else
				{
					if (!$line_cutoff_displayed)
					{
						// show array lines cutoff note
						$string = self::$data['max_lines_per_variable'].' line limit reached';

						$return3 = self::debug_array_cutoff($string, $array_name.'[...]', $org_array_name, $index+1);
						if (is_array($return3))
						{
							$return_array = array_merge($return_array, $return3);
						}

						$line_cutoff_displayed = true;
					}
				}
			}
		}
		return $return_array;
	}

	private static function debug_array_cutoff(&$variable, $variable_name, $org_variable_name='', $index = 0)
	{
		//$debug = false;
		//if ($debug) { echo "debug_string()<br>"; }
		//if ($debug) { echo "variable_name=".$variable_name."<br>"; }
		//if ($debug) { echo "org_variable_name=".$org_variable_name."<br>"; }
		//if ($debug) { echo "index=".$index."<br>"; }

		$string_lines = explode("\n", $variable);
		if (is_array($string_lines))
		{
			foreach ($string_lines as $key => $string_line)
			{
				$string_lines[$key] = str_replace("\r", "", $string_line);
			}
		}

		// track len into output_buffer_total_len so that we can output
		// when we hit a certain size.
		$string_len = strlen($variable);
		self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

		$return[] = array(
			'display' => 'variable',
			'name' => $variable_name,
			'type' => 'cutoff',
			'value' => $string_lines,
			'len' => $string_len,
		);

		//if ($debug) { echo "return="; print_r($return); echo "<br>"; }
		return $return;
	}

	private static function debug_object(&$object, $object_name, $org_object_name='', $index = 0, $params = array())
	{
		//$debug = false;
		//if ($debug) { echo "debug_object()<br>"; }
		$return_array = array();

		// generate list of variables.
		$arr = get_object_vars($object);

		foreach($arr as $prop => $val)
		{
			//echo "prop=<pre>";print_r($prop);echo"</pre><br>";
			$return2 = self::debug_string($val, $object_name . ' -> ' . $prop, $org_object_name, $index+1, $params);
			$return_array = array_merge($return_array, $return2);
		}

		//generate list of methods.
		$class = get_class($object);

		if ($class == "SimpleXMLElement")
		{
			//----------------------
			// get value of object
			//----------------------

			// convert object to array to get it's value
			$object_value = (array) $object;

			if (!empty($object_value))
			{
				$return2 = self::debug_string($object_value, $object_name, $org_object_name, $index+1, $params);
				//if ($debug) { echo "return2="; print_r($return2); echo "<br>"; }
				$return_array = array_merge($return_array, $return2);
				//if ($debug) { echo "return_array="; print_r($return_array); echo "<br>"; }
			}
		}
		else
		{
			$arr = get_class_methods($class);
			if (is_array($arr))
			{
				foreach ($arr as $method)
				{
					$string_len = strlen($method);
					self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

					$return[] = array(
						'display' => 'method',
						'name' => $object_name,
						'type' => '',
						'value' => array(
							0 => $method,
						),
						'len' => $string_len,
					);
					//if ($debug) { echo "return="; print_r($return); echo "<br>"; }
				}
				if (isset($return))
				{
					$return_array = array_merge($return_array, $return);
				}
			}
			//if ($debug) { echo "return_array="; print_r($return_array); echo "<br>"; }
		}

		//return $str;
		return $return_array;
	}

	private static function debug_string(&$variable, $variable_name, $org_variable_name='', $index = 0, $params = array())
	{
		//echo "debug_string_start=".memory_get_usage()."<br>";
		//$debug = false;
		//if ($debug) { echo "debug_string()<br>"; }
		//if ($debug) { echo "variable_name=".$variable_name."<br>"; }
		//if ($debug) { echo "org_variable_name=".$org_variable_name."<br>"; }
		//if ($debug) { echo "index=".$index."<br>"; }

		$ok_to_proceed = true;
		$hide_global_variables = false;

		// test for 'GLOBAL'
		$pos = strpos($variable_name, 'GLOBAL');
		if ($pos === false)
		{
		}
		else
		{
			$hide_global_variables = true;
		}

		if ($hide_global_variables)
		{
			//-------------------------------------------------
			// Prevent looping and hide duplicate GLOBAL info
			//-------------------------------------------------
			$do_not_allow_names = array(
				'debug[GLOBALS]',
				'GLOBALS[GLOBALS]',

				// remove predefine variables
				'GLOBALS[_COOKIE]',
				'GLOBALS[HTTP_COOKIE_VARS]',
				'GLOBALS[_ENV]',
				'GLOBALS[HTTP_ENV_VARS]',
				'GLOBALS[_FILES]',
				'GLOBALS[HTTP_POST_FILES]',
				'GLOBALS[_GET]',
				'GLOBALS[HTTP_GET_VARS]',
				'GLOBALS[_POST]',
				'GLOBALS[HTTP_POST_VARS]',
				'GLOBALS[_REQUEST]',
				'GLOBALS[_SERVER]',
				'GLOBALS[HTTP_SERVER_VARS]',
				'GLOBALS[_SESSION]',
				'GLOBALS[HTTP_SESSION_VARS]',

				// remove variables that can be found in $_SERVER
				'GLOBALS[PHP_SELF]',
				'GLOBALS[argv]',
				'GLOBALS[argc]',
				'GLOBALS[GATEWAY_INTERFACE]',
				'GLOBALS[SERVER_ADDR]',
				'GLOBALS[SERVER_NAME]',
				'GLOBALS[SERVER_SOFTWARE]',
				'GLOBALS[SERVER_PROTOCOL]',
				'GLOBALS[REQUEST_METHOD]',
				'GLOBALS[REQUEST_TIME]',
				'GLOBALS[QUERY_STRING]',
				'GLOBALS[DOCUMENT_ROOT]',
				'GLOBALS[HTTP_ACCEPT]',
				'GLOBALS[HTTP_ACCEPT_CHARSET]',
				'GLOBALS[HTTP_ACCEPT_ENCODING]',
				'GLOBALS[HTTP_ACCEPT_LANGUAGE]',
				'GLOBALS[HTTP_CONNECTION]',
				'GLOBALS[HTTP_HOST]',
				'GLOBALS[HTTP_REFERER]',
				'GLOBALS[HTTP_USER_AGENT]',
				'GLOBALS[HTTPS]',
				'GLOBALS[REMOTE_ADDR]',
				'GLOBALS[REMOTE_HOST]',
				'GLOBALS[REMOTE_PORT]',
				'GLOBALS[SCRIPT_FILENAME]',
				'GLOBALS[SERVER_ADMIN]',
				'GLOBALS[SERVER_PORT]',
				'GLOBALS[SERVER_SIGNATURE]',
				'GLOBALS[PATH_TRANSLASTED]',
				'GLOBALS[SCRIPT_NAME]',
				'GLOBALS[REQUEST_URI]',
				'GLOBALS[PHP_AUTH_DIGEST]',
				'GLOBALS[PHP_AUTH_USER]',
				'GLOBALS[PHP_AUTH_PW]',
				'GLOBALS[AUTH_TYPE]',

				// remove globals set by apache
				'GLOBALS[HTTP_UA_CPU]',
				'GLOBALS[HTTP_COOKIE]',

				'[GLOBALS][GLOBALS]'
			);

			// loop through $_ENV variables and eliminate those
			if (is_array($_ENV))
			{
				foreach($_ENV as $variable_name => $var_value)
				{
					$do_not_allow_names[] = 'GLOBALS['.$variable_name.']';
				}
			}
			// loop through $_REQUEST variables and eliminate those
			// $_REQUEST includes $_GET, $_POST, and $_COOKIE
			if (is_array($_REQUEST))
			{
				foreach($_REQUEST as $variable_name => $var_value)
				{
					$do_not_allow_names[] = 'GLOBALS['.$variable_name.']';
				}
			}


			if (is_array($do_not_allow_names))
			{
				foreach($do_not_allow_names as $key => $do_not_allow_name)
				{
					$pos = strpos($variable_name, $do_not_allow_name);
					if ($pos === false)
					{
					}
					else
					{
						$ok_to_proceed = false;
					}
				}
			}
		}

		if ($ok_to_proceed)
		{
			if (is_object($variable))
			{
				//return self::debug_object($variable, $variable_name . " (" . get_class($variable) ." Object) ", $org_variable_name, $index);
				$class_name = get_class($variable);
				$namespace_pos = strrpos($class_name, '\\');
				if ($namespace_pos > 0)
				{
					$class_name = substr($class_name, $namespace_pos+1);
				}
				return self::debug_object($variable, $variable_name . " (" . $class_name .") ", $org_variable_name, $index);
			}
			elseif (is_array($variable))
			{
				if (count($variable) == 0)
				{
					$string_len = 0;
					self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

					$return[] = array(
						'display' => 'variable',
						'name' => $variable_name,
						'type' => 'empty array',
						'value' => array(
							0 => '',
						),
						//'len' => $string_len,
					);
					return $return;
				}
				else
				{
					return self::debug_array($variable, $variable_name, $org_variable_name, $index, $params);
				}
			}
			elseif (is_real($variable) || is_float($variable) || is_double($variable))
			{
				$string_len = 0;
				self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

				$return[] = array(
					'display' => 'variable',
					'name' => $variable_name,
					'type' => 'double',
					'value' => array(
						0 => $variable,
					),
					//'len' => $string_len,
				);
				return $return;
			}
			elseif (is_long($variable) || is_int($variable))
			{
				$string_len = 0;
				self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

				$return[] = array(
					'display' => 'variable',
					'name' => $variable_name,
					'type' => 'long',
					'value' => array(
						0 => $variable,
					),
					//'len' => $string_len,
				);
				return $return;
			}
			elseif (is_string($variable))
			{
				//if ($debug) { echo "string...<br>"; }
				$string_lines = explode("\n", $variable);
				if (is_array($string_lines))
				{
					foreach ($string_lines as $key => $string_line)
					{
						$string_lines[$key] = str_replace("\r", "", $string_line);
					}
				}

				// track len into output_buffer_total_len so that we can output
				// when we hit a certain size.
				$string_len = strlen($variable);
				self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

				$return[] = array(
					'display' => 'variable',
					'name' => $variable_name,
					'type' => 'string',
					'value' => $string_lines,
					'len' => $string_len,
				);

				//if ($debug) { echo "return="; print_r($return); echo "<br>"; }
				return $return;

			}
			elseif (is_bool($variable))
			{
				if ($variable)
				{
					$string_len = 0;
					self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

					$return[] = array(
						'display' => 'variable',
						'name' => $variable_name,
						'type' => 'bool',
						'value' => array(
							0 => 'TRUE',
						),
						//'len' => $string_len,
					);
					return $return;
				}
				else
				{
					$string_len = 0;
					self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

					$return[] = array(
						'display' => 'variable',
						'name' => $variable_name,
						'type' => 'bool',
						'value' => array(
							0 => 'FALSE',
						),
						//'len' => $string_len,
					);
					return $return;
				}
			}
			elseif (is_resource($variable))
			{
				$string_len = 0;
				self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

				$return[] = array(
					'display' => 'variable',
					'name' => $variable_name,
					'type' => 'resource',
					'value' => get_resource_type($variable),
					//'len' => $string_len,
				);
				return $return;
			}
			else
			{
				$string_len = 0;
				self::$output_buffer_total_len += self::$data['output_buffer_per_line_len'] + $string_len;

				$return[] = array(
					'display' => 'variable',
					'name' => $variable_name,
					'type' => 'unknown',
					'value' => $variable,
					//'len' => $string_len,
				);
				return $return;
			}
		}
		//echo "debug_string_end=".memory_get_usage()."<br>";
	}

//-----------------------------------------------
// print variable methods
//-----------------------------------------------

	public static function print_var($variable, $variable_name="debug", $show_color = true)
	{
		if (!isset($variable))
		{
			$return_string = '###color###'.$variable_name.'###/color###' . " = (not set)###debug_br###";
		}
		else
		{
			$return_string = self::print_string($variable, $variable_name, $variable_name, $index = 0);
		}
		if ($show_color)
		{
			$return_string = htmlentities($return_string, ENT_QUOTES);
		}

		if ($show_color)
		{
			$return_string = str_replace('###debug_br###', '<br>', $return_string);
		}
		else
		{
			$return_string = str_replace('###debug_br###', "\n", $return_string);
		}
		if ($show_color)
		{
			$return_string = str_replace('###color###', '<span style="color:#052594;">', $return_string);
			$return_string = str_replace('###/color###', '</span>', $return_string);
			$return_string = '<div style="color:#333333; background-color:#ffffff;padding-left:5px;padding-right:5px;padding-bottom:5px;">'.$return_string.'</div>';
		}
		else
		{
			$return_string = str_replace('###color###', '', $return_string);
			$return_string = str_replace('###/color###', '', $return_string);
		}

		echo $return_string;
	}

	private static function print_array($array, $array_name, $org_array_name='', $index = 0)
	{
		if (!isset($str))
		{
			$str = '';
		}
		foreach($array as $var => $val)
		{
			$str .= self::print_string($val, $array_name . '[' . $var . ']', $org_array_name, $index+1)."";
		}
		return $str;
	}

	private static function print_object($object, $object_name, $org_object_name='', $index = 0)
	{
		$str = '';

		// generate list of variables.
	    $arr = get_object_vars($object);

		foreach($arr as $prop => $val)
		{
			$str .= self::print_string($val, $object_name . ' -> ' . $prop, $org_object_name, $index+1)."";
		}

		//generate list of methods.
		$class = get_class($object);

		if ($class == "SimpleXMLElement")
		{
			//----------------------
			// get value of object
			//----------------------

			// convert object to array to get it's value
			$object_value = (array) $object;

			if (!empty($object_value))
			{
			   $str .= self::print_string($object_value, $object_name, $org_object_name, $index+1)."";
			}
		}
		else
		{
			$arr = get_class_methods($class);
			foreach ($arr as $method)
			{
				$str .= '###color###'.$object_name.'###/color###' . ' -> ' . $method . "()###debug_br###\n";
			}
		}

	    return $str;
	}

	private static function print_string($variable, $variable_name, $org_variable_name='', $index = 0)
	{
		$ok_to_proceed = true;
		$hide_global_variables = false;

		// test for 'GLOBAL'
		$pos = strpos($variable_name, 'GLOBAL');
		if ($pos === false)
		{
		}
		else
		{
			$hide_global_variables = true;
		}

		if ($hide_global_variables)
		{
			//-------------------------------------------------
			// Prevent looping and hide duplicate GLOBAL info
			//-------------------------------------------------
			$do_not_allow_names = array(
				'debug[GLOBALS]',
				'GLOBALS[GLOBALS]',

				// remove predefine variables
				'GLOBALS[_COOKIE]',
				'GLOBALS[HTTP_COOKIE_VARS]',
				'GLOBALS[_ENV]',
				'GLOBALS[HTTP_ENV_VARS]',
				'GLOBALS[_FILES]',
				'GLOBALS[HTTP_POST_FILES]',
				'GLOBALS[_GET]',
				'GLOBALS[HTTP_GET_VARS]',
				'GLOBALS[_POST]',
				'GLOBALS[HTTP_POST_VARS]',
				'GLOBALS[_REQUEST]',
				'GLOBALS[_SERVER]',
				'GLOBALS[HTTP_SERVER_VARS]',
				'GLOBALS[_SESSION]',
				'GLOBALS[HTTP_SESSION_VARS]',

				// remove variables that can be found in $_SERVER
				'GLOBALS[PHP_SELF]',
				'GLOBALS[argv]',
				'GLOBALS[argc]',
				'GLOBALS[GATEWAY_INTERFACE]',
				'GLOBALS[SERVER_ADDR]',
				'GLOBALS[SERVER_NAME]',
				'GLOBALS[SERVER_SOFTWARE]',
				'GLOBALS[SERVER_PROTOCOL]',
				'GLOBALS[REQUEST_METHOD]',
				'GLOBALS[REQUEST_TIME]',
				'GLOBALS[QUERY_STRING]',
				'GLOBALS[DOCUMENT_ROOT]',
				'GLOBALS[HTTP_ACCEPT]',
				'GLOBALS[HTTP_ACCEPT_CHARSET]',
				'GLOBALS[HTTP_ACCEPT_ENCODING]',
				'GLOBALS[HTTP_ACCEPT_LANGUAGE]',
				'GLOBALS[HTTP_CONNECTION]',
				'GLOBALS[HTTP_HOST]',
				'GLOBALS[HTTP_REFERER]',
				'GLOBALS[HTTP_USER_AGENT]',
				'GLOBALS[HTTPS]',
				'GLOBALS[REMOTE_ADDR]',
				'GLOBALS[REMOTE_HOST]',
				'GLOBALS[REMOTE_PORT]',
				'GLOBALS[SCRIPT_FILENAME]',
				'GLOBALS[SERVER_ADMIN]',
				'GLOBALS[SERVER_PORT]',
				'GLOBALS[SERVER_SIGNATURE]',
				'GLOBALS[PATH_TRANSLASTED]',
				'GLOBALS[SCRIPT_NAME]',
				'GLOBALS[REQUEST_URI]',
				'GLOBALS[PHP_AUTH_DIGEST]',
				'GLOBALS[PHP_AUTH_USER]',
				'GLOBALS[PHP_AUTH_PW]',
				'GLOBALS[AUTH_TYPE]',

				// remove globals set by apache
				'GLOBALS[HTTP_UA_CPU]',
				'GLOBALS[HTTP_COOKIE]',

				'[GLOBALS][GLOBALS]'
			);

			// loop through $_ENV variables and eliminate those
			if (is_array($_ENV))
			{
				foreach($_ENV as $variable_name => $var_value)
				{
					$do_not_allow_names[] = 'GLOBALS['.$variable_name.']';
				}
			}
			// loop through $_REQUEST variables and eliminate those
			// $_REQUEST includes $_GET, $_POST, and $_COOKIE
			if (is_array($_REQUEST))
			{
				foreach($_REQUEST as $variable_name => $var_value)
				{
					$do_not_allow_names[] = 'GLOBALS['.$variable_name.']';
				}
			}

			if (is_array($do_not_allow_names))
			{
				foreach($do_not_allow_names as $key => $do_not_allow_name)
				{
					$pos = strpos($variable_name, $do_not_allow_name);
					if ($pos === false)
					{
					}
					else
					{
						$ok_to_proceed = false;
					}
				}
			}
		}

		if ($ok_to_proceed)
		{
			if (is_object($variable))
			{
				return self::print_object($variable, $variable_name . " (" . get_class($variable) ." Object) ", $org_variable_name, $index);
			}
			elseif (is_array($variable))
			{
				if (count($variable) == 0)
				{
					return '###color###'.$variable_name.'###/color###' . " = (empty array)###debug_br###";
				}
				else
				{
					return self::print_array($variable, $variable_name, $org_variable_name, $index);
				}
			}
			elseif (is_double($variable))
			{
				return '###color###'.$variable_name.'###/color###' . " = (double) ".$variable."###debug_br###";
			}
			elseif (is_long($variable))
			{
				return '###color###'.$variable_name.'###/color###' . " = (long) ".$variable."###debug_br###";
			}
			elseif (is_string($variable))
			{
				return '###color###'.$variable_name.'###/color###' . " = (string) '".$variable."'###debug_br###";
			}
			elseif (is_bool($variable))
			{
				if ($variable)
				{
					return '###color###'.$variable_name.'###/color###' . " = (bool) TRUE###debug_br###";
				}
				else
				{
					return '###color###'.$variable_name.'###/color###' . " = (bool) FALSE###debug_br###";
				}
			}
			elseif (is_resource($variable))
			{
				return '###color###'.$variable_name.'###/color###' . " = (resource) ".get_resource_type($variable)."###debug_br###";
			}
			else
			{
				return '###color###'.$variable_name.'###/color###' . " = (unknown) '".$variable."'###debug_br###";
			}
		}
	}

//-------------------------------------------------
// general supporting methods...
//-------------------------------------------------

	//-------------------------------------------------
	// STRING METHODS
	//-------------------------------------------------

	private static function remove_leading($input_string, $match_string)
	{
		if (self::starts_with($input_string, $match_string))
		{
			return self::mid($input_string, strlen($match_string)+1);
		}
		else
		{
			return $input_string;
		}
	}

	private static function starts_with($input_string, $match_string)
	{
		$match_len = strlen($match_string);
		if ($match_string == self::left($input_string, $match_len))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private static function left($input_string, $str_length)
	{
		$output_string = substr($input_string, 0, $str_length);
		return $output_string;
	}

	// first character is position 1
	private static function mid($str, $start, $howManyCharsToRetrieve = 0)
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

	private static function remove_trailing($input_string, $match_string)
	{
		if (self::ends_with($input_string, $match_string))
		{
			return self::left($input_string, strlen($input_string) - strlen($match_string));
		}
		else
		{
			return $input_string;
		}
	}

	private static function ends_with($input_string, $match_string)
	{
		$input_len = strlen($input_string);
		$match_len = strlen($match_string);
		if ($input_len > 0 && $match_len > 0)
		{
			if (self::right($input_string, $match_len) == $match_string)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}

	private static function right($str, $howManyCharsFromRight)
	{
		$strLen = strlen ($str);
		return substr ($str, $strLen - $howManyCharsFromRight, $strLen);
	}

	private static function in_str($mystring, $findme)
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

	//-------------------------------------------------
	// FILE METHODS
	//-------------------------------------------------

	private static function read_file($file_path)
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

	private static function append_file($output, $to_file)
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

	private static function create_dir($dir_name, $dir_mod=0777)
	{
		//echo "create_dir<br>";
		// make sure that path is using all forward slashes
		$dir_name = str_replace('\\', '/', $dir_name);

		$dir_name_array = explode('/', $dir_name);

		$check_dir = "";
		if (isset($dir_name_array))
		{
			foreach ($dir_name_array as $key => $dir_part)
			{
				//echo "dir_part=<pre>";print_r($dir_part);echo "</pre><br>";

				if (strlen(trim($dir_part)) > 0)
				{
					if (self::ends_with($dir_part, ':'))
					{
						$check_dir .= $dir_part;
					}
					else
					{
						$check_dir .= "/" . $dir_part;
					}

					// this code right here will generate open_basedir errors
					// without the @ symbol on the functions
					// when the open_basedir setting is restricted
					if (!self::ends_with($check_dir, ':'))
					{
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

	private static function write_file($output, $to_file)
	{
		if (strlen($output) > 0)
		{
			// Make directory if it doesn't exist.
			$dir_name = dirname($to_file);
			//echo "dir_name=<pre>";print_r($dir_name);echo "</pre><br>";

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

	private static function copy_file($from_file, $to_file)
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

	//-------------------------------------------------
	// DATE METHODS
	//-------------------------------------------------

	private static function get_micro_time()
	{
		list($sec, $usec) = explode(" ", microtime());
		return ($sec + $usec);
	}

	private static function display_micro_time_diff($start_time, $end_time)
	{
		//echo "start_time=<pre>";print_r($start_time);echo "</pre><br>";
		//echo "end_time=<pre>";print_r($end_time);echo "</pre><br>";
		$diff = $end_time - $start_time;
		if ($diff < 0.0001)
		{
			$return = "0.0000";
		}
		else
		{
			$return = substr(($diff), 0, 6);
		}
		//echo "return=<pre>";print_r($return);echo "</pre><br>";

		return $return;
	}

	private static function convert_unix_date($unixdate, $dateformat="yyyy-mm-dd hh:mm:ss")
	{
		$strDate = "";
		if (strlen($unixdate) > 0)
		{
			switch ($dateformat)
			{
				case "mm/dd/yyyy hh:mm:ss":
					$strDate  = str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."/";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)."/";
					$strDate .= self::year($unixdate)." ";
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;

				case "yyyy-mm-dd hh:mm:ss":
					$strDate = self::year($unixdate)."-";
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)." ";
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;
			}
		}
		return $strDate;
	}

	private static function year($datetime=0)
	{
		// Returns the numeric year
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['year'];
	}

	private static function mon($datetime=0)
	{
		// Returns the numeric month
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['mon'];
	}

	private static function mday($datetime=0)
	{
		// Returns the numeric day of month
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['mday'];
	}

	private static function hour($datetime=0)
	{
		// Returns the numeric hour
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['hours'];
	}

	private static function minute($datetime=0)
	{
		// Returns the numeric minute
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['minutes'];
	}

	private static function second($datetime=0)
	{
		// Returns the numeric second
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['seconds'];
	}

}

?>