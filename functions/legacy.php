<?php
//===================================================================
// z9Debug
//===================================================================
// legacy.php
// --------------------
// The set of debug functions to make php coding easier.
//
//       Date Created: 2005-04-23
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

//---------------------------------------------------------------------------------------
// Now we can see the state of any variable by just doing:
//
//		$debug = true;
//		if ($debug) { echo debug_var($my_variable, 'variable_name'); }
//
// We can see the value of all variables currently defined in the PHP namespace with:
//
//		echo debug_var($GLOBALS, "GLOBALS");
//
// For simplicity, GLOBALS is filtered of values that can found in $_SERVER and
// other predefined variables such as:
//
// 		_COOKIE, HTTP_COOKIE_VARS, _ENV, HTTP_ENV_VARS, _FILES, HTTP_POST_FILES,
// 		_GET, HTTP_GET_VARS, _POST, HTTP_POST_VARS, _REQUEST, _SERVER, HTTP_SERVER_VARS,
// 		_SESSION, HTTP_SESSION_VARS
//
// We can log the output to /debug_log.htm with:
//
//		reset_debug_log();
//		log_var($my_variable, 'my_variable');
//
//--------------------------------------------------------------------------------------

//------------------------------------------------------------
// 2008-05-23, Allan Vest, AllMerchants,
// reset_debug_log() added
//------------------------------------------------------------

//------------------------------------------------------------
// 2008-02-01, Allan Vest, AllMerchants,
// log_var() added
//------------------------------------------------------------


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