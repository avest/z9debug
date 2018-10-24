<?php
//===================================================================
// z9Debug
//===================================================================
// more.tpl.php
// --------------------
// "more" view file.
//
//       Date Created: 2017-03-18
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

use Facade\Str;
use Z9\Debug\Console\Facade\Value;
use Facade\Action;

$last_calling_file = '';
$last_calling_line = '';
$last_calling_function = '';
$last_calling_class = '';
$last_calling_class_type = '';

$return_string = '';
$curr_file_line_count = 0;
// loop on all outputs


if (is_array($var_data))
{
	foreach ($var_data as $var_data_key => $output)
	{
		if ($var_data_key == $show_var_data_key)
		{

			// process a single output
			$calling_file = $output['calling_file'];
			$calling_line = $output['calling_line'];
			$calling_function = $output['calling_function'];
			$calling_class = $output['calling_class'];
			$calling_class_type = $output['calling_class_type'];

			// process lines
			if (isset($output['lines']) && is_array($output['lines']))
			{
				foreach ($output['lines'] as $lines_key => $line)
				{
					if ($lines_key >= $show_lines_key)
					{
						switch ($line['display'])
						{
							case 'string':
								//debug::string('string...');
								// 'display' => 'string'
								// 'name' => '',
								// 'type' => '',
								// 'value' => array(
								//   0 => 'testing123...',
								// )

								$value_lines = Value::display_value_lines($line['value']);
								debug::variable($value_lines);

								$content = '';
								$content .= '<table id=tm><tr><td id=l>';
								$content .= $calling_line;
								$content .= '</td><td id=s>';
								$content .= $value_lines;
								$content .= '</td></tr></table>'."\n";

								$return_string .= $content;
								debug::variable($return_string);

								break;

							case 'variable':
								if (empty($line['name']) || Str::starts_with($line['name'], '###EMPTY###'))
								{
									$line['name'] = Action::_('Z9\Debug\Console\CodeFile')->get_variable_name($session_id, $request_id, $calling_file, $calling_line, $line['name']);
								}
								//debug::string('variable...');
								// 'display' => 'variable'
								// 'name' => 'my_array[first]',
								// 'type' => 'string',
								// 'value' => array(
								//   0 => 'Allan',
								// )
								$value_lines = Value::display_value_lines($line['value']);
								$value_wrap = Value::display_value_wrap($line['type']);

								$content = '';
								$content .= '<table id=tm><tr><td id=l>';
								$content .= $calling_line;
								$content .= '</td><td id=v><div id=n>';
								$content .= $line['name'];
								$content .= '</div> = (';
								$content .= $line['type'];
								if ($line['type'] == 'string' && $line['len'] > 0)
								{
									$content .= ':'.$line['len'];
								}
								$content .= ') <div id=q>';
								$content .= $value_wrap.$value_lines.$value_wrap;
								$content .= '</div></td></tr></table>'."\n";


								$return_string .= $content;
								debug::variable($return_string);

								break;

							case 'method':
								if (empty($line['name']) || starts_with($line['name'], '###EMPTY###'))
								{
									$line['name'] = Action::_('Z9\Debug\Console\CodeFile')->get_variable_name($session_id, $request_id, $calling_file, $calling_line, $line['name']);
								}
								//debug::string('method...');
								// 'display' => 'method'
								// 'name' => 'my_class',
								// 'type' => '',
								// 'value' => array(
								//   0 => 'method',
								//),
								$value_lines = Value::display_value_lines($line['value']);

								$content = '';
								$content .= '<table id=tm><tr><td id=l>';
								$content .= $calling_line;
								$content .= '</td><td id=v><div id=n>';
								$content .= $line['name'];
								$content .= '</div> -> <div id=n>';
								$content .= $value_lines;
								$content .= '() </div></td></tr></table>'."\n";

								$return_string .= $content;
								debug::variable($return_string);

								break;
						} // end switch

					}
				}  // end foreach

				$last_calling_file = $calling_file;
				$last_calling_line = $calling_line;
				$last_calling_function = $calling_function;
				$last_calling_class = $calling_class;
				$last_calling_class_type = $calling_class_type;
			}
		} // end if isset()
	} // end foreach ($var_data)

	echo $return_string;

} // end is_array($var_data)
?>