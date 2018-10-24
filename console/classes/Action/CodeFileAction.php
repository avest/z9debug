<?php
//===================================================================
// z9Debug
//===================================================================
// CodeFileAction.php
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
use Facade\Str;

class CodeFileAction
{
	public $cache = array(
		'variable_names' => array(),
		'session_id' => '',
		'request_id' => '',
		'file_path' => '',
		'file_handle' => '',
	);
//	$_DEBUG['variable_names'] = array();
//	$_DEBUG['session_id'] = '';
//	$_DEBUG['request_id'] = '';
//	$_DEBUG['file_path'] = '';
//	$_DEBUG['file_handle'] = '';

	function get_variable_name($session_id, $request_id, $file_path, $line_number, $org_variable_name)
	{
		// we are using a global variable here on purpose to cache a list of variable names already processed!
//		global $_DEBUG;
		debug::on(false);

		debug::variable($session_id);
		debug::variable($request_id);
		debug::variable($file_path);
		debug::variable($line_number);
		debug::variable($org_variable_name);

		$post_variable_name = Str::remove_leading($org_variable_name, '###EMPTY###');
		debug::variable($post_variable_name);

		if (isset($this->cache['variable_names'][$session_id][$request_id][$file_path][$line_number]))
		{
			$variable_name = $this->cache['variable_names'][$session_id][$request_id][$file_path][$line_number];
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
				$this->cache['session_id'] == $session_id &&
				$this->cache['request_id'] == $request_id &&
				$this->cache['file_path'] == $file_path
			)
			{
				$use_file_content = true;
			}

			// set $file_handle
			if ($use_file_content)
			{
				$file_handle = $this->cache['file_handle'];
			}
			else
			{
				// the new method should be faster
				// this methods adds approximately .00065 seconds per debug:variable call.
				// so, 1000 debug calls would add .65 seconds.
				if (is_file($session_file_path))
				{
					$file_handle = new \SplFileObject($session_file_path);
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
				$variable_name =$this->parse_file_line_for_variable_name($file_line);
			}
		}

		// cache the variable name
		if (!empty($variable_name))
		{
			$this->cache['variable_names'][$session_id][$request_id][$file_path][$line_number] = $variable_name;
		}

		// return the variable name with the post_variable_name appended
		$variable_name .= $post_variable_name;
		debug::variable($variable_name);

		return $variable_name;
	}

	public function parse_file_line_for_variable_name($file_line)
	{

		// PREG_MATCH METHOD
		preg_match('/(.*?)(debug)(.*?)(::)(.*?)(variable)(.*\(?)(.*)(.*\)?)(.*?)(;)(.*?)/', $file_line, $matches);
		debug::variable($matches);

		$variable_name = $matches[7];
		$variable_name = Str::remove_leading($variable_name, '(');
		$variable_name = Str::remove_trailing($variable_name, ')');
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

}

?>