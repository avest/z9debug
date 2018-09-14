<?php
//===================================================================
// z9Debug
//===================================================================
// debug_no_op.php
// --------------------
// The set of debug functions to make php coding easier.
//
//       Date Created: 2005-04-23
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

class debug
{
	public static $variable_name = '';
	public static $show_color = '';
	public static $log_to_file = '';
	public static $log_to_screen = '';
	public static $log_file = '';
	public static $backtrace = NULL;
	public static $output = array();
	public static $output_buffer = array();
	public static $output_buffer_total_len = 0;
	public static $output_buffer_page_count = 0;
	public static $start_page_load_time = '';
	public static $end_page_load_time = '';
	public static $page_load_time = '';
	public static $page_peak_memory = '';
	public static $page_sql_time = 0;
	public static $enabled = array();
	public static $enabled_file = '';
	public static $enabled_function = '';
	public static $timers = array();
	public static $sql_timers = array();
	public static $data = array();
	public static $request_full_url = '';
	public static $request_url_path = '';
	public static $request_url_query = '';
	public static $request_date = '';
	public static $web_root = '';
	public static $session_name = '';
	public static $cms_user = array();
	public static $cms_page = array();
	public static $php_code_files = array();

	public static function construct()
	{
	}

	public static function load_toggle_settings()
	{
	}

	public static function force_on($file, $function)
	{
	}

	public static function get($var_name)
	{
	}

	public static function set($var_name, $var_value)
	{
	}

	public static function get_output_data()
	{
	}

	public static function add_output_data($data)
	{
	}

	public static function on($is_turn_on=true)
	{
	}

	public static function off($is_turn_off=true)
	{
	}

	public static function is_on($data=array())
	{
	}

	public static function sql_start($sql, $backtrace)
	{
	}

	public static function sql_end($sql_id)
	{
	}

	public static function timer_start($timer_name)
	{
	}

	public static function timer_end($timer_id)
	{
	}

	public static function start_page_load_timer()
	{
	}

	public static function stop_page_load_timer()
	{
	}

	public static function set_cms_user($data)
	{
	}

	public static function set_cms_page($data)
	{
	}

	public static function set_page_peak_memory()
	{
	}

	public static function save_php_code_files()
	{
	}

	public static function variable($variable, $variable_name='', $params=array())
	{
	}

	public static function constant($variable, $variable_name='', $params=array())
	{
	}

	public static function micro_timestamp($string='')
	{
	}

	public static function timestamp($string='')
	{
	}

	public static function string($string, $params=array())
	{
	}

	public static function memory($params=array())
	{
	}

	public static function stack_trace()
	{
	}

	public static function str_exit($string='', $params=array())
	{
	}

	public static function debug_var(&$variable, $variable_name="debug", $show_color = true, $log_to_file = false, $log_to_screen = true, $log_file = null, $backtrace = null, $params = array())
	{
	}

	public static function debug_str($string, $show_color = true, $log_to_file = false, $log_to_screen = true, $log_file = null, $backtrace = null, $params = array())
	{
	}

	public static function has_output()
	{
	}

	public static function enabled($boolean=true)
	{
	}

	public static function suppress_output($boolean=true)
	{
	}

	public static function session_name($session_name='')
	{
	}

	public static function default_limit($default_limit=50)
	{
	}

	public static function save_page_data()
	{
	}

	public static function save_var_data()
	{
	}


	public static function save_timer_data()
	{
	}

	public static function save_sql_data()
	{
	}

	public static function save_file_data()
	{
	}

	public static function save_global_data()
	{
	}

	public static function save_cms_data()
	{
	}

	public static function display_console_link()
	{
	}

	public static function print_var($variable, $variable_name="debug", $show_color = true)
	{
	}


}

?>