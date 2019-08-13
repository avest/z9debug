<?php
//===================================================================
// z9Debug
//===================================================================
// load.php
// --------------------
// The set of debug functions to make php coding easier.
//
//       Date Created: 2005-04-23
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

define('Z9DEBUG_DIR', dirname( __FILE__ ));
include_once(Z9DEBUG_DIR . '/classes/debug.php');
include_once(Z9DEBUG_DIR . '/settings/config_settings.php');

debug::construct();

function z9debug_shutdown()
{
	debug::stop_page_load_timer();
	debug::set_page_peak_memory();

	if (debug::has_output())
	{
		debug::save_var_data();

		// save request_full_url
		// save request_date
		// save page load time
		// save page peak memory
		// save sql query time
		// must save the page data after the var data to capture the correct output_buffer_page_count.
		debug::save_page_data();

		debug::save_timer_data();

		debug::save_sql_data();

		debug::save_file_data();

		debug::save_global_data();

		debug::save_cms_data();

		debug::save_php_code_files();

		if (!debug::get('suppress_output'))
		{
			debug::display_console_link();
		}
	}

}

// Note: if you want to debug code called by other
// shutdown functions, then you would need to make
// this register call last.
register_shutdown_function('z9debug_shutdown');

//include_once(Z9DEBUG_DIR . '/functions/legacy.php');

// maintain backwards compatible
function debug_var($variable, $variable_name="debug", $show_color = true, $log_to_file = false, $log_to_screen = true)
{
	$log_file = null;
	$backtrace = debug_backtrace();
	$return_string = debug::debug_var($variable, $variable_name, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace);
	return $return_string;
}

// maintain backwards compatible
function debug_str($string, $show_color=true, $log_to_file=false, $log_to_screen=true)
{
	$log_file = null;
	$backtrace = debug_backtrace();
	$return_string = debug::debug_str($string, $show_color, $log_to_file, $log_to_screen, $log_file, $backtrace);
	return $return_string;
}

?>