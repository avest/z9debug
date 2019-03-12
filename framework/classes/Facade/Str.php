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


// function alternate_value($is_default_value, $default_value, $alt_value)
// function contains_spam_text($text_string)
// function convert_to_javascript($content)
// function convert_var_to_string($var_value, $var_name)
// function crc32fix($num)
// function display_boolean($boolean_value)
// function domnode_to_array($node)
// function echo_safe($data)
// function empty_array_to_str($value)
// function empty_if_zero($input_string)
// function ends_with($input_string, $match_string)
// function escape_string_for_javascript($input_string)
// function extract_image_source($html_str)
// function filter_non_digits($input_string)
// function first_char($input_string)
// function get_all_between_matches($start, $end, $string)
// function html($input_string)
// function html_decode($value)
// function left($input_string, $str_length)
// function is_empty($input_string)
// function in_str($mystring, $findme)
// function last_char($input_string)
// funciton mkPasswd()
// function mid($str, $start, $howManyCharsToRetrieve = 0)
// function parse_first_name($name)
// function parse_last_name($name)
// function remove_first_char($input_string)
// function remove_last_char($input_string)
// function remove_leading($input_string, $match_string)
// function remove_trailing($input_string, $match_string)
// function removeEvilAttributes($tagSource)
// function removeEvilTags($source)
// function right ($str, $howManyCharsFromRight)
// function show_ascii_values_for_string($string)
// function starts_with($input_string, $match_string)
// function str_empty($string)
// function strip_tags_keep_comments($input, $allowable_tags = "")
// function stripslashes_safe($input)
// function truncate_middle_of_str($input_string, $no_of_leading_chars, $no_of_trailing_chars, $replace_middle_with)
// function utf8_string_encode($url)
// function xmlstr_to_array($xmlstr)

namespace Facade;

use debug;

class Str
{
	public function _construct()
	{
	}

	public static function alternate_value($is_default_value, $default_value, $alt_value)
	{
		if ($is_default_value)
		{
			echo $default_value;
		}
		else
		{
			echo $alt_value;
		}
		//return true;
	}

	public static function contains_spam_text($text_string)
	{
		$stop_words = array(
			'<b>',
			'</b>',
			'<em>',
			'</em>',
			'<h1>',
			'</h1>',
			'<i>',
			'</i>',
			'<script',
			'<strong>',
			'</strong>',
			'<title>',

			'href=',
			'href =',
			'url=',
			'[/URL]',
			'[URL=',

			'acyclovir',
			'adderall',
			'adipex',
			'alprazolam',
			'ambien',
			'atarax',
			'ativan',
			'bontril',
			'butalbital',
			'carisoprodol',
			'celexa',
			'cialis',
			'cipro',
			'codeine',
			'dianabol',
			'diazepam',
			'didrex',
			'diflucan',
			'effexor',
			'famvir',
			'fioricet',
			'hydrocodone',
			'levitra',
			'lipitor',
			'lorazepam',
			'lortab',
			'meridia',
			'nexium',
			'norco',
			'paxil',
			'pharmacy',
			'phentermine',
			'propecia',
			'prozac',
			'renova',
			'soma',
			'tamiflu',
			'tramadol',
			'tylenol',
			'wellbutrin',
			'ultram',
			'valium',
			'valtrex',
			'viagra',
			'vicodin',
			'xanax',
			'xenical',
			'zoloft',
			'zovirax',
			'zyban',
			'zyrtec',

			'casino',
			'poker',
			'texas holdem',

			'penis',

			chr(224),
			chr(225),
			chr(226),
			chr(227),
			chr(228),
			chr(229),
			chr(230),
			chr(231),
			//chr(232),
			//chr(233),
			chr(234),
			chr(235),
			chr(236),
			chr(237),
			chr(238),
			chr(239),
			chr(240),
			chr(241),
			chr(242),
			//chr(243),
			//chr(244),
			chr(245),
			//chr(246),
			//chr(247),
			//chr(248),
			//chr(249),
			//chr(250),
			chr(251),
			chr(252),
			chr(253),
			chr(254),
			chr(255),
		);

		$is_spam = false;
		$text_string = strtolower($text_string);
		foreach ($stop_words as $key => $stop_word)
		{
			if (self::in_str($text_string, $stop_word))
			{
				$is_spam = true;
			}
		}

		return $is_spam;
	}

	public static function convert_to_javascript($content)
	{
		$content = str_replace("'", "\'", $content);
		$content = str_replace(chr(10), " ", $content);
		$content = str_replace(chr(13), " ", $content);
		$content = str_replace("\t", " ", $content);
		$output = "<script>\r\n";
		$output .= "document.write('".$content."');\r\n";
		$output .= "</script>\r\n";
		return $output;
	}

	public static function convert_var_to_string($var_value, $var_name)
	{
		$return = '$'.$var_name.' = ';
		$return .= stripslashes(var_export($var_value, true));
		$return .= "\r\n";
		return $return;
	}

	// The normal crc32 function will return a different result
	// when run on a 32-bit or 64-bit architecture.
	// This function works on 32-bit or 64-bit architecture.
	public static function crc32fix($num)
	{
		$crc = crc32($num);
		if($crc & 0x80000000)
		{
			$crc ^= 0xffffffff;
			$crc += 1;
			$crc = -$crc;
		}
		return $crc;
	}

	//----------------------------------------------------------------------
	// 	Name:  display_boolean
	// 	Description:
	//		This function returns "TRUE" for true and "FALSE" for false
	//	Input:  boolean_value
	//	Output:  return_value
	//----------------------------------------------------------------------
	public static function display_boolean($boolean_value)
	{
		if ($boolean_value == true || $boolean_value == 1)
		{
			$return_value = "TRUE";
		}
		else
		{
			$return_value = "FALSE";
		}
		return $return_value;
	}

	public static function echo_safe($data)
	{
		// this function is needed when sending data to gpg_encrypt
		// assuming data has 'addslashes' already
		$data = stripslashes($data);
		// \ -> \\
		$data = str_replace("\\", "\\\\", $data);
		// $ -> \$
		$data = str_replace("$", "\\$", $data);
		// " -> \"
		$data = str_replace("\"", "\\\"", $data);
		// ' -> \'
		// stays the same!
		//$data = str_replace("'", "\\'", $data);
		return $data;
	}

	// if string, return string
	// if empty array, return empty string
	// else return value passed in
	public static function empty_array_to_str($value)
	{
		if (is_string($value))
		{
			return $value;
		}
		if (is_array($value) && empty($array))
		{
			return '';
		}
		return $value;
	}

	public static function empty_if_zero($input_string)
	{
		if ($input_string == 0)
		{
			return "";
		}
		else
		{
			return $input_string;
		}
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

	public static function escape_string_for_javascript($input_string)
	{
		$output_string = $input_string;
		// escape any single quotes
		$output_string = str_replace("'", "\'", $output_string);
		// html encode any double quotes
		$output_string = str_replace('"', '&quot;', $output_string);
		return $output_string;
	}

	public static function extract_image_source($html_str)
	{
		$return = '';
		if (!empty($html_str))
		{
			$doc = new DOMDocument();
			@$doc->loadHTML($html_str);
			$xml = simplexml_import_dom($doc);
			$images = $xml->xpath('//img');

			if (is_array($images))
			{
				$image = $images[0];
			}
			$return = (string)$image['src'];
		}
		return $return;
	}

	public static function filter_non_digits($input_string)
	{
		$output_string = '';
		$len = strlen($input_string);
		if ($len > 0)
		{
			for ($i = 0; $i <= ($len - 1); $i++)
			{
				//$output_string .= ( ereg("^[0-9\.]+$", $input_string[$i])) ? $input_string[$i] : '';
				$output_string .= ( preg_match("/^[0-9\.]+$/", $input_string[$i])) ? $input_string[$i] : '';
			}
		}
		return $output_string;
	}

	public static function first_char($input_string)
	{
		return self::left($input_string, 1);
	}

	// give a $string,
	// return an array of substrings that are found
	// between a $start substring and an $end substring
	public static function get_all_between_matches($start, $end, $string)
	{
		debug::string("preg_match_all_clean()");
		debug::variable($start, 'start');
		debug::variable($end, 'end');
		debug::variable($string, 'string');

		$res = array();
		// while start and end are both found in string
		while(strpos($string, $start) !== FALSE && strpos($string, $end) !== FALSE)
		{
			// find pos of start substring
			$first = strpos($string, $start);
			debug::variable($first, 'first');
			// remove everthing before start substring
			$string = substr($string, $first);
			debug::variable($string, 'string');
			// find pos of end substring
			$last = strpos($string, $end);
			debug::variable($last, 'last');
			// capture between start substring and end substring
			$res[] = substr($string, strlen($start), $last - strlen($start));
			debug::variable($res, 'res');

			// find end of first end substring
			$length = $last + strlen($end);
			debug::variable($length, 'length');

			// remove everything after first end substring
			$string = substr($string, $length);
			debug::variable($string, 'string');

		}
		return $res;
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

	// html_entity_decode() doesn't translate &apos; to a single quote
	public static function html_decode($value)
	{
		$value = html_entity_decode($value, ENT_QUOTES);
		$value = str_replace("&apos;", "'", $value);
		return $value;
	}

	public static function is_empty($input_string)
	{
		if (strlen($input_string) == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
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

	// +----------------------------------------------------------------------+
	// | PHP Pronounceable Password Generator                                 |
	// +----------------------------------------------------------------------+
	// | Author: Max Dobbie-Holman <max@blueroo.net>                          |
	// +----------------------------------------------------------------------+
	//
	// View the demo at http://www.blueroo.net/max/pwdgen.php

	/**
	 * Generates an 8 character pronounceable password.
	 *
	 * @author        Max Dobbie-Holman <max@blueroo.net>
	 * @version       1.0
	 */

	public static function mkPasswd()
	{
		$consts='bcdgklmnprst';
		$vowels='aeiou';

		mt_srand ((double) microtime() * 1000000);

		for ($x=0; $x < 6; $x++)
		{
			$constant = substr($consts, mt_rand(0, strlen($consts)-1), 1);
			$vowel = substr($vowels, mt_rand(0, strlen($vowels)-1), 1);

			debug::variable($constant, 'constant');
			debug::variable($vowel, 'vowel');
			$const[$x] = $constant;
			$vow[$x] = $vowel;
		}
		debug::variable($const, 'const');
		debug::variable($vow, 'vow');

		$return = $const[0] . $vow[0] .$const[2] . $const[1] . $vow[1] . $const[3] . $vow[3] . $const[4];
		debug::variable($return, 'return');

		return $return;
	}

	// zazZazaz12
	public static function mkPasswd_strong()
	{
		$consts='bcdgklmnprst';
		$vowels='aeiou';
		$digits='0123456789';

		mt_srand ((double) microtime() * 1000000);

		for ($x=0; $x < 6; $x++)
		{
			$constant = substr($consts, mt_rand(0, strlen($consts)-1), 1);
			$vowel = substr($vowels, mt_rand(0, strlen($vowels)-1), 1);
			$digit = substr($digits, mt_rand(0, strlen($digits)-1), 1);

			debug::variable($constant, 'constant');
			debug::variable($vowel, 'vowel');
			debug::variable($digit, 'digit');

			$const[$x] = $constant;
			$vow[$x] = $vowel;
			$dig[$x] = $digit;
		}
		debug::variable($const, 'const');
		debug::variable($vow, 'vow');
		debug::variable($dig, 'dig');

		$return = strtoupper($const[0]) . $vow[0] .$const[2] . strtoupper($const[1]) . $vow[1] . $const[3] . $vow[3] . $const[4] . $dig[5] . $dig[1];
		debug::variable($return, 'return');

		return $return;
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

	public static function parse_first_name($name)
	{
		$p = explode(" ", $name);
		$first_name = '';
		if (is_array($p))
		{
			foreach($p as $key => $value)
			{
				if ($key == 0)
				{
					$first_name .= $value." ";
				}
			}
		}
		$first_name = trim($first_name);
		return $first_name;
	}

	public static function parse_last_name($name)
	{
		$p = explode(" ", $name);
		$last_name = '';
		if (is_array($p))
		{
			foreach($p as $key => $value)
			{
				if ($key > 0)
				{
					$last_name .= $value." ";
				}
			}
		}
		$last_name = trim($last_name);
		return $last_name;
	}

	public static function removeEvilAttributes($tagSource)
	{
		$stripAttrib = "' (style|class)=\"(.*?)\"'i";
		$tagSource = stripslashes($tagSource);
		$tagSource = preg_replace($stripAttrib, '', $tagSource);
		return $tagSource;
	}

// TODO - fix this
	public static function removeEvilTags($source)
	{
		$allowedTags='<a><br><b><h1><h2><h3><h4><i>' .
			'<img><li><ol><p><strong><table>' .
			'<tr><td><th><u><ul>';
		$source = strip_tags($source, $allowedTags);
		return preg_replace('/<(.*?)>/ie', "'<'.removeEvilAttributes('\\1').'>'", $source);
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

	public static function replace_leading($input_string, $match_string, $new_string)
	{
		$output = self::remove_leading($input_string, $match_string);
		if ($output <> $input_string)
		{
			$output = $new_string.$output;
		}
		return $output;
	}

	public static function replace_trailing($input_string, $match_string, $new_string)
	{
		$output = self::remove_trailing($input_string, $match_string);
		if ($output <> $input_string)
		{
			$output = $output.$new_string;
		}
		return $output;
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

	public static function show_ascii_values_for_string($string)
	{
		if (strlen($string) > 0)
		{
			$i = 1;
			while ($i <= strlen($string))
			{
				$char = self::mid($string, $i, 1);
				echo $char."=".ord($char)."<br>";
				$i++;
			}
		}
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

	public static function str_empty($string)
	{
		if (strlen($string) <= 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function strip_tags_keep_comments($input, $allowable_tags = "")
	{
		$output = $input;

		$output = str_replace("<!--", "###COMMENT###!--", $output);
		$output = str_replace("-->", "--###COMMENT###", $output);

		$output = strip_tags($output, $allowable_tags);

		$output = str_replace("###COMMENT###!--", "<!--", $output);
		$output = str_replace("--###COMMENT###", "-->", $output);

		return $output;
	}

	public static function truncate_middle_of_str($input_string, $no_of_leading_chars, $no_of_trailing_chars, $replace_middle_with)
	{
		$output_string = '';
		if (!empty($input_string))
		{
			$input_len = strlen($input_string);
			debug::variable($input_len, 'input_len');

			$max_output_len = $no_of_leading_chars + $no_of_trailing_chars + strlen($replace_middle_with);
			debug::variable($max_output_len, 'max_output_len');

			if ($input_len <= $max_output_len)
			{
				$output_string = $input_string;
			}
			else
			{
				// get leading characters
				$output_string = self::left($input_string, $no_of_leading_chars);

				// append middle string
				$output_string .= $replace_middle_with;

				// get trailing characters
				$output_string .= self::right($input_string, $no_of_trailing_chars);
			}
		}
		return $output_string;
	}

	public static function utf8_string_encode($url)
	{
		return htmlentities($url, ENT_QUOTES, 'UTF-8');
	}

	/**
	 * convert xml string to php array - useful to get a serializable value
	 *
	 * @param string $xmlstr
	 * @return array
	 * @author Adrien aka Gaarf
	 * http://gaarf.info/2009/08/13/xml-string-to-php-array/
	 * released under a do-whatever-but-dont-sue-me license
	 */
	// 2018-04-09: Has moved here:
	// https://github.com/gaarf/XML-string-to-PHP-array
	//
	// There are sites using xmlstr_to_array($xmlstr) without the always_add_value_index setting
	//
	// NOTE: For any new calls to this function, be sure to set always_add_value_index = true

	//$xml = '
	//<Request deploymentMode="production">
	//	<OrderRequest>
	//		<ItemOut lineNumber="1" quantity="1" requestedDeliveryDate="">
	//			<ItemDetail>
	//				<Extrinsic name="lineItemID">147fdb72-e53c-e811-80ec-0cc47a7eded9</Extrinsic>
	//			</ItemDetail>
	//		</ItemOut>
	//	</OrderRequest>
	//</Request>
	//';

	//-----------------------------------------------------
	// capture_attrib_for_single_value_tag = false
	//-----------------------------------------------------
	//$xml_array[OrderRequest][ItemOut][ItemDetail][Extrinsic] = (string:36) '147fdb72-e53c-e811-80ec-0cc47a7eded9'
	//$xml_array[OrderRequest][ItemOut][@attributes][lineNumber] = (string:1) '1'
	//$xml_array[OrderRequest][ItemOut][@attributes][quantity] = (string:1) '1'
	//$xml_array[OrderRequest][ItemOut][@attributes][requestedDeliveryDate] = (string) ''
	//$xml_array[@attributes][deploymentMode] = (string:10) 'production'

	//-----------------------------------------------------
	// capture_attrib_for_single_value_tag = true
	//-----------------------------------------------------
	// Extrinsic is converted to array.
	// The value of Extrinsic is save as [@value]
	//$xml_array[OrderRequest][ItemOut][ItemDetail][Extrinsic][@value] = (string:36) '147fdb72-e53c-e811-80ec-0cc47a7eded9'
	// The attribute are added to the Extrinsic array.
	//$xml_array[OrderRequest][ItemOut][ItemDetail][Extrinsic][@attributes][name] = (string:36) 'lineItemID'
	//$xml_array[OrderRequest][ItemOut][@attributes][lineNumber] = (string:1) '1'
	//$xml_array[OrderRequest][ItemOut][@attributes][quantity] = (string:1) '1'
	//$xml_array[OrderRequest][ItemOut][@attributes][requestedDeliveryDate] = (string) ''
	//$xml_array[@attributes][deploymentMode] = (string:10) 'production'

	public static function xmlstr_to_array($xmlstr, $always_add_value_index=false)
	{
		debug::on(false);
		debug::variable($xmlstr, 'xmlstr');
		debug::variable($always_add_value_index);

		$doc = new \DOMDocument();
		$doc->loadXML($xmlstr);
		return self::domnode_to_array($doc->documentElement, $always_add_value_index);
	}


	// supports xmlstr_to_array function
	private function domnode_to_array($node, $always_add_value_index)
	{
		debug::on(false);
		debug::string('START');
		//debug::variable($node);
		//debug::variable($always_add_value_index);

		$output = array();
		debug::variable($node->nodeType);

		if (debug::is_on())
		{
			$xml = $node->ownerDocument->saveXML($node);
			debug::variable($xml);
		}

		//debug::variable(XML_CDATA_SECTION_NODE); // 4
		//debug::variable(XML_TEXT_NODE); // 3
		//debug::variable(XML_ELEMENT_NODE); // 1
		switch ($node->nodeType)
		{
			case XML_CDATA_SECTION_NODE: // 4
			case XML_TEXT_NODE: // 3
				$output = trim($node->textContent);
				debug::variable($output);
				break;
			case XML_ELEMENT_NODE: // 1
				for ($i=0, $m=$node->childNodes->length; $i<$m; $i++)
				{
					debug::variable($i);
					debug::variable($m);

					$child = $node->childNodes->item($i);
					//debug::variable($child);

					$v = self::domnode_to_array($child, $always_add_value_index);
					debug::variable($v);

					if (isset($child->tagName))
					{
						debug::variable($child->tagName);
						$t = $child->tagName;
						debug::variable($t);

						if (!isset($output[$t]))
						{
							$output[$t] = array();
							debug::variable($output);
						}
						// Z9 Digital
						if ($always_add_value_index && is_array($v) && empty($v))
						{
							$output[$t][] = array(
								'@value' => $v,
							);
						}
						else
						{
							$output[$t][] = $v;
						}
						debug::variable($output);
					}
					elseif ($v || $v == '0')
					{
						// Z9 Digital
						if ($always_add_value_index)
						{
							$output = array(
								'@value' => $v,
							);
						}
						else
						{
							$output = (string) $v;
						}
						debug::variable($output);
					}
				}

				debug::variable($node->attributes->length);

				if ($always_add_value_index)
				{
					if ($node->attributes->length && !is_array($output))
					{
						//Has attributes but isn't an array
						$output = array('@value' => $output); //Change output into an array.
						debug::variable($output);
					}
				}

				if (is_array($output))
				{
					if ($node->attributes->length)
					{
						$a = array();
						debug::variable($a);
						foreach ($node->attributes as $attrName => $attrNode)
						{
							debug::variable($attrName);
							debug::variable($attrNode);
							$a[$attrName] = (string) $attrNode->value;
							debug::variable($a[$attrName], 'a['.$attrName.']');
						}
						$output['@attributes'] = $a;
						debug::variable($output['@attributes']);
					}
					foreach ($output as $t => $v)
					{
						debug::variable($t);
						debug::variable($v);
						if (is_array($v) && count($v) == 1 && $t != '@attributes')
						{
							$output[$t] = $v[0];
							debug::variable($output[$t], 'output['.$t.']');
						}
					}
				}
				//else
				//{
				//	// Z9 Digital, add
				//	if ($node->attributes->length)
				//	{
				//		$a = array();
				//		debug::variable($a);
				//		foreach ($node->attributes as $attrName => $attrNode)
				//		{
				//			debug::variable($attrName);
				//			debug::variable($attrNode);
				//			$a[$attrName] = (string) $attrNode->value;
				//			debug::variable($a[$attrName], 'a['.$attrName.']');
				//		}
				//		$output = array(
				//			'@value' => $output,
				//			'@attributes' => $a,
				//		);
				//		debug::variable($output);
				//	}
				//}
				break;
		}
		debug::variable($output, 'return output');
		debug::string('END');

		return $output;
	}


	public static function test_xmlstr_to_array()
	{
		debug::on();
		$xml = '<cXML version="1.2.005" xml:lang="en-US" payloadID="127fdb72-e53c-e811-80ec-0cc47a7eded9" timestamp="2018-04-10T13:34:55">
<Header>
	<From>
		<Credential domain="DUNS">
			<Identity>DD072DE1-D904-E611-80CB-0025907FDF4B</Identity>
		</Credential>
		<Credential domain="CompanyName">
			<Identity>Proforma Q62</Identity>
		</Credential>
	</From>
	<To>
		<Credential domain="DUNS">
			<Identity />
		</Credential>
		<Credential domain="CompanyName">
			<Identity>Sprocket Express</Identity>
		</Credential>
	</To>
	<Sender>
		<Credential domain="DUNS">
			<Identity>000000000</Identity>
			<SharedSecret>JhOkQwNh71</SharedSecret>
		</Credential>
		<UserAgent>OrderForge</UserAgent>
	</Sender>
</Header>
<Request deploymentMode="production">
	<OrderRequest>
		<OrderRequestHeader orderID="MF-3-1" orderDate="2018-04-10T10:34:46" type="new">
			<Total>
				<Money currency="USD">0.00</Money>
			</Total>
			<BillTo>
				<Address addressID="">
					<Name xml:lang="en-US"></Name>
					<PostalAddress name="Proforma Q62">
						<DeliverTo></DeliverTo>
						<Street>8800 E Pleasant Valley Rd</Street>
						<Street></Street>
						<Street></Street>
						<City>Independence</City>
						<State>OH</State>
						<PostalCode>44131</PostalCode>
						<Country isoCountryCode="US">United States</Country>
					</PostalAddress>
					<Email></Email>
					<Phone>
						<TelephoneNumber>
						<CountryCode isoCountryCode="US" />
						<AreaOrCityCode />
						<Number />
						</TelephoneNumber>
					</Phone>
				</Address>
			</BillTo>
			<Shipping>
				<Money currency="USD">0.00</Money>
				<Description xml:lang="en-US"></Description>
			</Shipping>
			<Tax>
				<Money currency="USD">0.00</Money>
				<Description xml:lang="en-US"></Description>
			</Tax>
			<Comments xml:lang="en-US" />
			<Extrinsic name="OrderType">New Order</Extrinsic>
			<Extrinsic name="OrderFields"></Extrinsic>
			<Extrinsic name="endCustomerOrderIDList">MF-3</Extrinsic>
		</OrderRequestHeader>
		<ItemOut lineNumber="1" quantity="1" requestedDeliveryDate="">
			<ItemID>
				<SupplierPartID>Thank You Card Envelope</SupplierPartID>
				<SupplierPartAuxiliaryID>Thank You Card Envelope</SupplierPartAuxiliaryID>
			</ItemID>
			<ItemDetail>
				<UnitPrice>
					<Money currency="USD">0.000000</Money>
				</UnitPrice>
				<Description xml:lang="en-US">Thank You Card Envelope</Description>
				<UnitOfMeasure>EA</UnitOfMeasure>
				<Classification domain="" />
				<URL>https://orderforge.foundrycommerce.com/Output/General/Production/A0799618/A0799618_00001.pdf</URL>
				<Extrinsic name="lineItemID">147fdb72-e53c-e811-80ec-0cc47a7eded9</Extrinsic>
				<Extrinsic name="productType">Variable</Extrinsic>
				<Extrinsic name="quantityMultiplier">1</Extrinsic>
				<Extrinsic name="costCenter"></Extrinsic>
				<Extrinsic name="costCenterInteropID"></Extrinsic>
				<Extrinsic name="OrderLineFields"></Extrinsic>
				<Extrinsic name="ProductSpecs">
					<Extrinsic name="v07_Address1"></Extrinsic>
					<Extrinsic name="v08_Address2"></Extrinsic>
					<Extrinsic name="v09_City"></Extrinsic>
					<Extrinsic name="v10_State"></Extrinsic>
					<Extrinsic name="v11_Zip"></Extrinsic>
					<Extrinsic name="v05_Location"></Extrinsic>
				</Extrinsic>
				<Extrinsic name="productInteropID"></Extrinsic>
				<Extrinsic name="variantInteropID"></Extrinsic>
				<Extrinsic name="shippingAddressInteropID"></Extrinsic>
				<Extrinsic name="shipWeight">0.01</Extrinsic>
				<Extrinsic name="requestedShipper"></Extrinsic>
				<Extrinsic name="requestedShippingAccount"></Extrinsic>
				<Extrinsic name="UserName">testuser@windriver</Extrinsic>
				<Extrinsic name="endCustomerOrderID">MF-3</Extrinsic>
				<Extrinsic name="endCustomerDuns" />
				<Extrinsic name="UserFirstName">test</Extrinsic>
				<Extrinsic name="UserLastName">user</Extrinsic>
				<Extrinsic name="UserEmail">testuser@windriver</Extrinsic>
				<Extrinsic name="UserPhone" />
			</ItemDetail>
			<ShipTo>
				<Address addressID="">
					<Name xml:lang="en-US">All American</Name>
					<PostalAddress name="Kline\'s">
						<DeliverTo>All American</DeliverTo>
						<Street>6 Riga Lane</Street>
						<Street />
						<Street />
						<City>Birdsboro</City>
						<State>PA</State>
						<PostalCode>19508</PostalCode>
						<Country isoCountryCode="US">United States</Country>
					</PostalAddress>
					<Email />
					<Phone>
						<TelephoneNumber>
							<CountryCode isoCountryCode="US" />
							<AreaOrCityCode />
							<Number />
						</TelephoneNumber>
					</Phone>
				</Address>
			</ShipTo>
		</ItemOut>
	</OrderRequest>
</Request>
</cXML>';

		if (false)
		{
			$xml = '
<Request deploymentMode="production">
	<OrderRequest>
		<ItemOut lineNumber="1" quantity="1" requestedDeliveryDate="">
			<ItemDetail>
				<Extrinsic name="lineItemID">147fdb72-e53c-e811-80ec-0cc47a7eded9</Extrinsic>
			</ItemDetail>
		</ItemOut>
	</OrderRequest>
</Request>
			';
		}

		debug::variable($xml);

		$xml_array = Str::xmlstr_to_array($xml, true);
		debug::variable($xml_array);

		// OLD
		//$xml_array[OrderRequest][ItemOut][ItemDetail][Extrinsic] = (string:36) '147fdb72-e53c-e811-80ec-0cc47a7eded9'
		//$xml_array[OrderRequest][ItemOut][@attributes][lineNumber] = (string:1) '1'
		//$xml_array[OrderRequest][ItemOut][@attributes][quantity] = (string:1) '1'
		//$xml_array[OrderRequest][ItemOut][@attributes][requestedDeliveryDate] = (string) ''
		//$xml_array[@attributes][deploymentMode] = (string:10) 'production'

		// NEW
		//$xml_array[OrderRequest][ItemOut][ItemDetail][Extrinsic][@value] = (string:36) '147fdb72-e53c-e811-80ec-0cc47a7eded9'
		//$xml_array[OrderRequest][ItemOut][ItemDetail][Extrinsic][@attributes][name] = (string:36) 'lineItemID'
		//$xml_array[OrderRequest][ItemOut][@attributes][lineNumber] = (string:1) '1'
		//$xml_array[OrderRequest][ItemOut][@attributes][quantity] = (string:1) '1'
		//$xml_array[OrderRequest][ItemOut][@attributes][requestedDeliveryDate] = (string) ''
		//$xml_array[@attributes][deploymentMode] = (string:10) 'production'

	}



	public static function is_serialized($str)
	{
		return ($str == serialize(false) || @unserialize($str) !== false);
	}

	public static function iso88591_to_html($string)
	{
		// https://www.w3schools.com/charsets/ref_html_8859.asp
		$string = str_replace(chr(133), "...", $string); // left single quote mark
		$string = str_replace(chr(145), "'", $string); // left single quote mark
		$string = str_replace(chr(146), "'", $string); // right single quote mark
		$string = str_replace(chr(147), '"', $string); // left double quote mark
		$string = str_replace(chr(148), '"', $string); // right double quote mark
		$string = str_replace(chr(149), '&bull;', $string);
		$string = str_replace(chr(150), '&ndash;', $string);
		$string = str_replace(chr(151), '&mdash;', $string);
		$string = str_replace(chr(152), '&tilde;', $string);
		$string = str_replace(chr(153), '&trade;', $string);
		$string = str_replace(chr(160), '&nbsp;', $string);
		$string = str_replace(chr(162), '&cent;', $string);
		$string = str_replace(chr(163), '&pound;', $string);
		$string = str_replace(chr(169), '&copy;', $string);
		$string = str_replace(chr(174), '&reg;', $string);
		$string = str_replace(chr(180), '&acute;', $string);
		$string = str_replace(chr(188), '&frac14;', $string);
		$string = str_replace(chr(189), '&frac12;', $string);
		$string = str_replace(chr(190), '&frac34;', $string);
		return $string;
	}

	public static function csv_iso88591_to_html($string)
	{
		// https://www.w3schools.com/charsets/ref_html_8859.asp
		$string = str_replace(chr(133), "...", $string); // left single quote mark
		$string = str_replace(chr(145), "'", $string); // left single quote mark
		$string = str_replace(chr(146), "'", $string); // right single quote mark
		$string = str_replace(chr(147), '@QUOTE@', $string); // left double quote mark
		$string = str_replace(chr(148), '@QUOTE@', $string); // right double quote mark
		$string = str_replace(chr(149), '&bull;', $string);
		$string = str_replace(chr(150), '&ndash;', $string);
		$string = str_replace(chr(151), '&mdash;', $string);
		$string = str_replace(chr(152), '&tilde;', $string);
		$string = str_replace(chr(153), '&trade;', $string);
		$string = str_replace(chr(160), '&nbsp;', $string);
		$string = str_replace(chr(162), '&cent;', $string);
		$string = str_replace(chr(163), '&pound;', $string);
		$string = str_replace(chr(169), '&copy;', $string);
		$string = str_replace(chr(174), '&reg;', $string);
		$string = str_replace(chr(180), '&acute;', $string);
		$string = str_replace(chr(188), '&frac14;', $string);
		$string = str_replace(chr(189), '&frac12;', $string);
		$string = str_replace(chr(190), '&frac34;', $string);
		return $string;
	}

	// if $active_tab == $this_tab, return "block", else "none"
	public static function tab_display_style($active_tab, $this_tab)
	{
		if ($this_tab == $active_tab)
		{
			$div_style = "block";
		}
		else
		{
			$div_style = "none";
		}
		return $div_style;
	}

}

?>