<?php

namespace Z9\Debug\Console\Facade;

use debug;

class Value
{
	public function _construct()
	{
	}

	public static function display_value_lines($lines)
	{
		debug::on(false);
		debug::variable($lines);

		$return = '';
		if (is_array($lines))
		{
			foreach ($lines as $line_key => $line)
			{
				$lines[$line_key] = htmlentities($lines[$line_key], ENT_QUOTES);
				//$lines[$line_key] = str_replace("\t", "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $lines[$line_key]);
			}
		}

		debug::variable($lines);

		$lines_count = (is_array($lines)) ? count($lines) : 0;
		debug::variable($lines_count);

		if (is_array($lines) && count($lines) == 0)
		{
			$return  = '';
		}
		if (is_array($lines) && count($lines) == 1)
		{
			$return = $lines[0];
		}
		if (is_array($lines) && count($lines) > 0)
		{
			if (!is_resource($lines) && is_array($lines))
			{
				$return = implode("<br>", $lines);
			}
			else
			{
				return $lines;
			}
		}

		return $return;
	}

	public static function display_value_wrap($type)
	{
		switch ($type)
		{
			case 'string':
				return "'";
				break;
			case 'not set':
				return "";
				break;
			case 'double':
				return "";
				break;
			case 'long':
				return "";
				break;
			case 'bool':
				return "";
				break;
			case 'unknown':
				return "";
				break;
		}
	}



}

?>