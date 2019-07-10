<?php
//===================================================================
// Z9 Framework
//===================================================================
// Str.php
// --------------------
//       Date Created: 2005-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================


namespace Facade;

use debug;

class Str
{
	public function _construct()
	{
	}

	public static function ends_with($input_string, $match_string)
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

	public static function first_char($input_string)
	{
		return self::left($input_string, 1);
	}

	public static function html($input_string)
	{
		// removed stripslashes call on 2018-04-19, not sure why it was ever there...
		// probably goes back to earliest days of php.
		//return htmlspecialchars(stripslashes($input_string),ENT_QUOTES);
		return htmlspecialchars($input_string,ENT_QUOTES);

		// this will fix issue with htmlspecialchars returning blank value.
		//htmlspecialchars($input_string,ENT_QUOTES, 'ISO-8859-1', true);
	}

	public static function in_str($mystring, $findme)
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

	public static function last_char($input_string)
	{
		return self::right($input_string, 1);
	}

	public static function left($input_string, $str_length)
	{
		$output_string = '';
		if (!empty($input_string))
		{
			$output_string = substr($input_string, 0, $str_length);
		}
		return $output_string;
	}

	public static function is_strong_password($password, $min_chars, $req_upper_and_lower, $req_char, $req_digit, $req_symbol, $req_digit_or_symbol=false)
	{
		debug::on(false);
		debug::variable($password);
		debug::variable($min_chars);
		debug::variable($req_upper_and_lower);
		debug::variable($req_char);
		debug::variable($req_digit);
		debug::variable($req_symbol);
		debug::variable($req_digit_or_symbol);

		$has_char = false;
		$has_upper = false;
		if (preg_match('/[A-Z]/', $password))
		{
			// There is one upper
			$has_upper = true;
			$has_char = true;
		}
		debug::variable($has_upper);
		debug::variable($has_char);

		$has_lower = false;
		if (preg_match('/[a-z]/', $password))
		{
			// There is one lower
			$has_lower = true;
			$has_char = true;
		}
		debug::variable($has_lower);
		debug::variable($has_char);

		$has_digit = false;
		if (preg_match('/[0-9]/', $password))
		{
			// There is one digit
			$has_digit = true;
		}
		debug::variable($has_digit);

		$is_strong = true;

		if (strlen($password) < $min_chars)
		{
			$is_strong = false;
			debug::variable($is_strong);
		}

		if ($req_upper_and_lower)
		{
			if (!$has_lower || !$has_upper)
			{
				$is_strong = false;
				debug::variable($is_strong);
			}
		}

		if ($req_char)
		{
			if (!$has_char)
			{
				$is_strong = false;
				debug::variable($is_strong);
			}
		}

		if ($req_digit)
		{
			if (!$has_digit)
			{
				$is_strong = false;
				debug::variable($is_strong);
			}
		}

		$symbols = array(
			'`',
			'~',
			'!',
			'@',
			'#',
			'$',
			'%',
			'^',
			'&',
			'*',
			'(',
			')',
			'_',
			'+',
			'-',
			'=',
			'{',
			'}',
			'|',
			'[',
			']',
			'\\',
			':',
			'"',
			';',
			'\'',
			'<',
			'>',
			'?',
			',',
			'.',
			'/',
		);
		debug::variable($symbols);

		$count_chars = count_chars($password, 1);
		debug::variable($count_chars);

		$has_symbol = false;
		if (is_array($symbols))
		{
			foreach ($symbols as $symbol)
			{
				$byte_value = ord($symbol);
				debug::variable($byte_value);

				if (isset($count_chars[$byte_value]) && $count_chars[$byte_value] > 0)
				{
					$has_symbol = true;
					break;
				}
			}
		}
		debug::variable($has_symbol);

		if ($req_symbol)
		{

			if (!$has_symbol)
			{
				$is_strong = false;
				debug::variable($is_strong);
			}

		}

		if ($req_digit_or_symbol)
		{
			if (!$has_symbol && !$has_digit)
			{
				$is_strong = false;
				debug::variable($is_strong);
			}
		}

		return $is_strong;
	}

	// first character is position 1
	public static function mid($str, $start, $howManyCharsToRetrieve = 0)
	{
		$return_value = '';
		if (!empty($str))
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
		}

		return $return_value;
	}

	public static function remove_first_char($input_string)
	{
		return self::mid($input_string, 2);
	}

	public static function remove_last_char($input_string)
	{
		if (strlen($input_string) <= 1)
		{
			return '';
		}
		else
		{
			return self::mid($input_string, 1, strlen($input_string)-1);
		}
	}

	public static function remove_leading($input_string, $match_string)
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

	public static function remove_trailing($input_string, $match_string)
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

	public static function right($str, $howManyCharsFromRight)
	{
		$output_string = '';
		if (!empty($str))
		{
			$strLen = strlen($str);
			$output_string = substr($str, $strLen - $howManyCharsFromRight, $strLen);
		}
		return $output_string;
	}

	public static function starts_with($input_string, $match_string)
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

}

?>