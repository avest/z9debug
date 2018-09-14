<?php
//===================================================================
// z9Debug
//===================================================================
// file.php
// --------------------
// file ajax call
//
//       Date Created: 2018-01-14
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

define('Z9DEBUG_CONSOLE', true);

define('Z9DEBUG_DIR', dirname(dirname( __FILE__ )));

include(Z9DEBUG_DIR.'/load_console.php');
debug::on(false);

include(Z9DEBUG_DIR.'/settings/config_settings.php');
if (is_file(Z9DEBUG_DIR.'/settings/toggle_settings.php'))
{
	include(Z9DEBUG_DIR.'/settings/toggle_settings.php');
}
if (is_file(Z9DEBUG_DIR.'/settings/file_categories.php'))
{
	include(Z9DEBUG_DIR.'/settings/file_categories.php');
}
$file_categories = debug::get('file_categories');

include(Z9DEBUG_DIR.'/console/functions/console.php');

$web_root = remove_leading(str_replace("\\", "/", Z9DEBUG_DIR), str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']));

$web_root = str_replace("\\", "/", $web_root);
debug::variable($web_root);

$is_authenticated = is_valid_auth_token();
debug::variable($is_authenticated);

if (!$is_authenticated)
{
	exit();
}

$data_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions';
debug::variable($data_dir);

$session_id = '';
if (isset($_POST['session_id']))
{
	$session_id = $_POST['session_id'];
}
debug::variable($session_id);

if (empty($session_id))
{
	exit();
}

$session_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions'.DIRECTORY_SEPARATOR.$session_id;
debug::variable($session_dir);

$request_id = '';
if (isset($_POST['request_id']))
{
	$request_id = $_POST['request_id'];
}
debug::variable($request_id);

if (empty($request_id))
{
	exit();
}

$request_dir = Z9DEBUG_DIR.'/sessions/'.$session_id.'/'.$request_id;
debug::variable($request_dir);

if (DIRECTORY_SEPARATOR == '/')
{
	// linux
	$request_dir = str_replace("\\", "/", $request_dir);
}
elseif (DIRECTORY_SEPARATOR == '\\')
{
	// windows
	$request_dir = str_replace("/", "\\", $request_dir);
}
debug::variable($request_dir);

$confirm_request_dir = realpath($request_dir);
debug::variable($confirm_request_dir);

if ($request_dir <> $confirm_request_dir)
{
	exit();
}

$file_data = get_file_data($request_dir);
debug::variable($file_data);

$force_on = debug::get('force_on');
debug::variable($force_on);

if (is_array($file_data['list']))
{
	foreach ($file_data['list'] as $file_key => $file)
	{
		if (is_array($file['functions_executed']))
		{
			foreach ($file['functions_executed'] as $function => $function_info)
			{
				$function_name = $function;
				if ($function == '-')
				{
					$function_name = '[file]';
				}
				else
				{
					$function_name .= '()';
				}
				$file_data['list'][$file_key]['functions_executed'][$function]['display_function'] = $function_name;


				// 'Mlaphp\Autoloader'
				$class_name_parts = parse_class_name($function_info['class']);
				debug::variable($class_name_parts);

				$file_data['list'][$file_key]['functions_executed'][$function]['namespace'] = $class_name_parts['namespace'];

				$file_data['list'][$file_key]['functions_executed'][$function]['class_name'] = $class_name_parts['class_name'];

				$toggled_on = false;
				if (is_toggled_on($function_info['file'], $class_name_parts['namespace'], $class_name_parts['class_name'], $function))
				{
					$toggled_on = true;
				}
				debug::variable($toggled_on);

				$file_data['list'][$file_key]['functions_executed'][$function]['toggled_on'] = $toggled_on;
			}
		}
	}
}
debug::variable($file_data['list']);

// testing...
if (false)
{
	$pattern = '(.*)(Controller)(.*)';
	$file_path = '/classes/Controller/Test.php';
	preg_match('/'.$pattern.'/', $file_path, $matches);
	debug::variable($matches);
	//debug::str_exit();
}

// sort files into categories
// loop through each category
if (true)
{
	$new_file_list = array();
	if (isset($file_categories))
	{
		if (is_array($file_categories))
		{
			foreach ($file_categories as $category_key => $category)
			{
				// loop through each matching rule of category
				if (is_array($category['include']))
				{
					foreach ($category['include'] as $patther_key => $pattern)
					{
						debug::variable($pattern);

						// loop through all of file list for each rule
						if (is_array($file_data['list']))
						{
							foreach ($file_data['list'] as $file_key => $file)
							{
								debug::variable($file_key);

								debug::variable($file);

								$file_path = $file['name'];
								debug::variable($file_path);

								// try to match $file_path to rule
								preg_match('/'.$pattern.'/', $file_path, $matches);
								debug::variable($matches);

								if (!empty($matches[0]))
								{
									// make sure $file_path is not in exclude list...
									$is_excluded = false;
									if (is_array($category['exclude']))
									{
										foreach ($category['exclude'] as $exclude_pattern_key => $exclude_pattern)
										{
											debug::variable($exclude_pattern);
											preg_match('/'.$exclude_pattern.'/', $file_path, $matches);
											debug::variable($matches);

											if (!empty($matches[0]))
											{
												$is_excluded = true;
												//break;
											}
										}
									}
									debug::variable($is_excluded);

									if (!$is_excluded)
									{
										debug::string('adding '.$file_path.' to '.$category_key);
										$file['category'] = $category_key;

										$new_file_list[] = $file;
										debug::variable($new_file_list);

										// speed up the process... reduce the file list size when a file is matched...
										unset($file_data['list'][$file_key]);

										// break from this loop if we found a match
										//break;
									}

								}


							}
						}
					}
				}
			}
		}
	}
	debug::variable($new_file_list);

	// add any unmatched file back
	if (is_array($file_data['list']))
	{
		foreach ($file_data['list'] as $file_key => $file)
		{
			$new_file_list[] = $file;
		}
	}


	$file_data['list'] = $new_file_list;
} // end sort by category



debug::variable($file_data);

include(Z9DEBUG_DIR.'/console/views/file.tpl.php');

?>