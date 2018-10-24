<?php
//===================================================================
// z9Debug
//===================================================================
// page.tpl.php
// --------------------
// "page" view file.
//
//       Date Created: 2017-10-10
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

	use Facade\Str;
	use Z9\Debug\Console\Facade\Value;
	use Facade\Action;

	debug::on(false);

	//[0] => Array
	//   (
	//	  [calling_file] => \classes\Block\home_online_courses_block.php
	//	  [calling_line] => 21
	//	  [calling_function] => display
	//	  [calling_class] => Block\home_online_courses_block
	//	  [calling_class_type] => ->
	//	  [lines] => Array
	//		 (
	//			[0] => Array
	//			    (
	//				   [display] => variable
	//				   [name] => date_now_adjusted
	//				   [type] => long
	//				   [value] => Array
	//					  (
	//						 [0] => 1438900997
	//					  )
	//
	//			    )
	//
	//		 )
	//
	//   )

	$last_calling_file = '';
	$last_calling_line = '';
	$last_calling_function = '';
	$last_calling_class = '';
	$last_calling_class_type = '';

	$return_string = '';
	$max_file_line_count = 35;
	$curr_file_line_count = 0;
	// loop on all outputs
	if (is_array($var_data))
	{
		foreach ($var_data as $var_data_key => $output)
		{
			// process a single output
			$calling_file = $output['calling_file'];
			debug::variable($calling_file);

			$calling_line = $output['calling_line'];
			$calling_function = $output['calling_function'];
			$calling_class = $output['calling_class'];
			$calling_class_type = $output['calling_class_type'];

			if ( $calling_file <> $last_calling_file ||
				$calling_function <> $last_calling_function ||
				$calling_class <> $last_calling_class ||
				$calling_class_type <> $last_calling_class_type  ||
				$calling_line <> $last_calling_line)
			{
				$curr_file_line_count = 0; // restart count
				debug::variable($curr_file_line_count);
			}

			// do we need to display location
			$display_location = false;
			if ( $calling_file <> $last_calling_file ||
				$calling_function <> $last_calling_function ||
				$calling_class <> $last_calling_class ||
				$calling_class_type <> $last_calling_class_type )
			{
				$display_location = true;
			}
			debug::variable($display_location);


			// display location
			if ($display_location)
			{
				// show location

				$document_root = $_SERVER['DOCUMENT_ROOT'];
				$document_root = str_replace('\\', '/', $document_root);

				$js_calling_file = $calling_file;
				$js_calling_file = str_replace('\\', '/', $js_calling_file);
				$js_calling_file = Str::remove_leading($js_calling_file, $document_root);
				debug::variable($js_calling_file);

				$js_calling_class = $calling_class;
				$namespace_pos = strrpos($js_calling_class, '\\');
				if ($namespace_pos > 0)
				{
					$js_calling_class = substr($js_calling_class, $namespace_pos+1);
				}

				$js_calling_class_type = $calling_class_type;

				$js_calling_function = $calling_function;

				$content = '';
				$content .= '<div id=hr>';
					$content .= '<div id=loc>';
					$content .= $js_calling_file . ' ';
					if (!empty($js_calling_class))
					{
						$content .= ': '.$js_calling_class.' ';
					}
					if (!empty($js_calling_function))
					{
						$is_include = false;
						if ( $js_calling_function == 'require_once' ||
							$js_calling_function == 'require' ||
							$js_calling_function == 'include' ||
							$js_calling_function == 'include_once'
						)
						{
							$is_include = true;
						}
						debug::variable($is_include);

						debug::variable($js_calling_class);
						if (!empty($js_calling_class) || (!$is_include && empty($js_calling_class)))
						{
							if (!empty($js_calling_class_type))
							{
								$func_sep = $js_calling_class_type.' ';
							}
							else
							{
								$func_sep = ': ';
							}
							$content .= $func_sep.$js_calling_function.'()';
						}
					}
					$content .= '</div>';
				$content .= '</div>'."\n";
				debug::variable($content);

				$return_string .= $content;
				debug::variable($return_string);
			}
			debug::variable($return_string);

			debug::variable($output['lines']);

			$start_new_line = true;

			// process lines
			if (isset($output['lines']) && is_array($output['lines']))
			{
				foreach ($output['lines'] as $lines_key => $line)
				{
					$curr_file_line_count++;
					if ($curr_file_line_count <= $max_file_line_count)
					{
						debug::variable($line['display']);
						debug::variable($line);
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
								if ($start_new_line)
								{
									$content .= '<table id=tborder><tr><td id=l>';
								}
								else
								{
									$content .= '<table id=t><tr><td id=l>';
								}
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
								if ($start_new_line)
								{
									$content .= '<table id=tborder><tr><td id=l>';
								}
								else
								{
									$content .= '<table id=t><tr><td id=l>';
								}
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
								//debug::string('method...');
								// 'display' => 'method'
								// 'name' => 'my_class',
								// 'type' => '',
								// 'value' => array(
								//   0 => 'method',
								//),
								if (empty($line['name']) || Str::starts_with($line['name'], '###EMPTY###'))
								{
									$line['name'] = Action::_('Z9\Debug\Console\CodeFile')->get_variable_name($session_id, $request_id, $calling_file, $calling_line, $line['name']);
								}

								$value_lines = Value::display_value_lines($line['value']);

								$content = '';
								if ($start_new_line)
								{
									$content .= '<table id=tborder><tr><td id=l>';
								}
								else
								{
									$content .= '<table id=t><tr><td id=l>';
								}
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
					else
					{
						debug::variable($line['display']);
						debug::variable($line);

						// DISPLAY MORE LINK
						if ($curr_file_line_count == $max_file_line_count + 1)
						{
							switch ($line['display'])
							{
								case 'string':
									$content = '';
									$content .= '<div id=m_'.$var_data_key.'_'.$lines_key.'>';
									$content .= '<table id=t><tr><td id=l>';
									$content .= $calling_line;
									$content .= '</td><td id=s><div id=mo class=mo_'.$var_data_key.'_'.$lines_key.'>';
									$content .= '&nbsp;<a class=nodec onclick="m(\''.$var_data_page.'\', \''.$var_data_key.'\',\''.$lines_key.'\')">[+] </a><a onclick="m(\''.$var_data_page.'\', \''.$var_data_key.'\',\''.$lines_key.'\')">more</a>&nbsp;';
									$content .= '</div></td></tr></table>';
									$content .= '</div>'."\n";
									$return_string .= $content;
									break;

								case 'variable':
									$content = '';
									$content .= '<div id=m_'.$var_data_key.'_'.$lines_key.'>';
									$content .= '<table id=t><tr><td id=l>';
									$content .= $calling_line;
									$content .= '</td><td id=v><div id=mo class=mo_'.$var_data_key.'_'.$lines_key.'>';
									$content .= '&nbsp;<a class=nodec onclick="m(\''.$var_data_page.'\', \''.$var_data_key.'\',\''.$lines_key.'\')">[+] </a><a onclick="m(\''.$var_data_page.'\', \''.$var_data_key.'\',\''.$lines_key.'\')">more</a>&nbsp;';
									$content .= '</div>';
									$content .= '</div></td></tr></table>';
									$content .= '</div>'."\n";
									$return_string .= $content;
									break;

								case 'method':
									$content = '';
									$content .= '<div id=m_'.$var_data_key.'_'.$lines_key.'>';
									$content .= '<table id=t><tr><td id=l>';
									$content .= $calling_line;
									$content .= '</td><td id=v><div id=mo class=mo_'.$var_data_key.'_'.$lines_key.'>';
									$content .= '&nbsp;<a class=nodec onclick="m(\''.$var_data_page.'\', \''.$var_data_key.'\',\''.$lines_key.'\')">[+] </a><a onclick="m(\''.$var_data_page.'\', \''.$var_data_key.'\',\''.$lines_key.'\')">more</a>&nbsp;';
									$content .= '</div>';
									$content .= '</div></td></tr></table>';
									$content .= '</div>'."\n";
									$return_string .= $content;
									break;
							} // end switch
						}
					} // end $curr_file_line_count <= 40

					$start_new_line = false;

				}  // end foreach

				$last_calling_file = $calling_file;
				$last_calling_line = $calling_line;
				$last_calling_function = $calling_function;
				$last_calling_class = $calling_class;
				$last_calling_class_type = $calling_class_type;

			} // end if isset()
		} // end foreach ($var_data)

		echo $return_string;

	} // end is_array($var_data)
?>
	<?php if ($var_data_page_count > 1): ?>
	<div class="content" style="padding-left:20px;">
		<br>
		<? if ($var_data_page > 1): ?>
			<a href="javascript:show_var(1)">Prev Page</a> &nbsp;&nbsp;
		<? endif; ?>

		<?php for ($i = 1; $i <= $var_data_page_count; $i++): ?>
			<?php if ($var_data_page == $i): ?>
				<b>[<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>]</b>&nbsp;
			<?php else: ?>
				<a href="javascript:show_var(<?php echo $i; ?>)">[<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>]</a>&nbsp;
			<?php endif; ?>
		<?php endfor; ?>

		<? if ($var_data_page < $var_data_page_count): ?>
			&nbsp;&nbsp; <a href="javascript:show_var(<?php echo $var_data_page_count; ?>)">Next Page</a>
		<? endif; ?>
		<br>&nbsp;
	</div>
	<?php endif; ?>

