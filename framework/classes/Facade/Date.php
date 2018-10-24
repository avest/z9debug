<?php
//===================================================================
// Z9 Framework
//===================================================================
// Date.php
// --------------------
//       Date Created: 2003-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

// function convert_date($input_date, $input_format="mm/dd/yyyy", $output_format="yyyy-mm-dd")
// function convert_mysql_date ($datetime, $dateformat="yyyy/m/d")
// function convert_mysql_time ($time, $timeformat="h:mm am")
// function convert_unix_date ($unixdate, $dateformat="yyyy-mm-dd hh:mm:ss", $now=false)
// function DateAdd ($interval, $number, $date)
// function DateDiff ($interval, $date1, $date2)
// function day_of_month_number_extension($day_of_month)
// function display_date ($datetime=0)
// function display_datetime ($datetime=0)
// function display_month($numeric_month)
// function display_month_abbr($numeric_month)
// function display_mysql_date ($datetime)
// function friendly_time_length($seconds, $decimals=1)
// function get_numeric_month_from_abbr($text_month)
// function get_numeric_month_from_name($text_month)
// function hour ($datetime=0)
// function is_am($datetime=0)
// function is_date_set ($datetime)
// function is_friday($datetime=0)
// function is_monday($datetime=0)
// function is_mysql_date_format($datetime)
// function is_pm($datetime=0)
// function is_thursday($datetime=0)
// function is_tuesday($datetime=0)
// function is_saturday($datetime=0)
// function is_sunday($datetime=0)
// function is_wednesday($datetime=0)
// function last_day_of_month($datetime=0)
// function log_timestamp()
// function mday ($datetime=0)
// function micro_time()
// function micro_time_diff()
// function minute ($datetime=0)
// function mon ($datetime=0)
// function month ($datetime=0)
// function month_name($datetime=0, $abbreviated = true, $numeric_month_input=false)
// function mysql_now($unix_now="", $output_format="yyyy-mm-dd hh:mm:ss")
// function second ($datetime=0)
// function today ($datetime=0)
// function wday ($datetime=0)
// function weekday ($datetime=0)
// function yday ($datetime=0)
// function year ($datetime=0)

namespace Facade;

use debug;
use DateTimeZone;
use DateTime;

class Date
{
	public function _construct()
	{
	}

	// convert an input date mm/dd/yyyy to yyyy-mm-dd
	public static function convert_date($input_date, $input_format="mm/dd/yyyy", $output_format="yyyy-mm-dd")
	{
		debug::on(false);
		debug::variable($input_date, 'input_date');
		debug::variable($input_format, 'input_format');
		debug::variable($output_format, 'output_format');

		$input_year = '';
		$input_month = '';
		$input_day = '';
		$input_hour = '';
		$input_minute = '';
		$input_second = '';

		switch ($input_format)
		{
			// January 22, 2014
			case "Month dd, yyyy":
				list($input_month, $input_day, $input_year) = explode(' ', $input_date);
				$input_year = intval($input_year);
				$input_month = self::get_numeric_month_from_name($input_month);
				$input_day = str_replace(',', '', $input_day);
				$input_day = intval($input_day);
				break;

			// 30-Sep-12
			case "dd-Mon-yy":
				list($input_day, $input_month, $input_year) = explode('-', $input_date);
				$input_year = 2000 + intval($input_year);
				$input_month = self::get_numeric_month_from_abbr($input_month);
				$input_day = intval($input_day);
				break;

			case "mm/dd/yyyy":
				$input_year = intval(substr($input_date, 6, 4));
				$input_month = intval(substr($input_date, 0, 2));
				$input_day = intval(substr($input_date, 3, 2));
				break;

			case "yyyymmdd":
				$input_year = intval(substr($input_date, 0, 4));
				$input_month = intval(substr($input_date, 4, 2));
				$input_day = intval(substr($input_date, 6, 2));
				break;

			case "mm/dd/yy":
				$input_year = intval(substr($input_date, 6, 2));
				if ($input_year < 50)
				{
					$input_year = 2000 + $input_year;
				}
				else
				{
					$input_year = 1900 + $input_year;
				}
				$input_month = intval(substr($input_date, 0, 2));
				$input_day = intval(substr($input_date, 3, 2));
				break;

			case "yyyy-mm-ddThh:mm:ss-ss:ss":
				$input_year = intval(substr($input_date, 0, 4));
				$input_month = intval(substr($input_date, 5, 2));
				$input_day = intval(substr($input_date, 8, 2));
				$input_hour = intval(substr($input_date, 11, 2));
				$input_minute = intval(substr($input_date, 14, 2));
				$input_second = intval(substr($input_date, 17, 2));
				break;

			case "yyyy-mm-dd hh:mm:ss.sss":
				$input_year = intval(substr($input_date, 0, 4));
				$input_month = intval(substr($input_date, 5, 2));
				$input_day = intval(substr($input_date, 8, 2));
				$input_hour = intval(substr($input_date, 11, 2));
				$input_minute = intval(substr($input_date, 14, 2));
				$input_second = intval(substr($input_date, 17, 2));
				break;

			case "yyyy-mm-ddThh:mm:ss.sss":
				$input_year = intval(substr($input_date, 0, 4));
				$input_month = intval(substr($input_date, 5, 2));
				$input_day = intval(substr($input_date, 8, 2));
				$input_hour = intval(substr($input_date, 11, 2));
				$input_minute = intval(substr($input_date, 14, 2));
				$input_second = intval(substr($input_date, 17, 2));
				break;

			case "m/d/yyyy":
				list($input_month, $input_day, $input_year) = explode('/', $input_date);
				break;

			case "m/d/yyyy h:mm":
				list($date_part, $time_part) = explode(' ', $input_date);
				list($input_month, $input_day, $input_year) = explode('/', $date_part);
				list($input_hour, $input_minute) = explode(':', $time_part);
				break;

			case "h:mm am": // am/pm optional
				$input_date = trim($input_date);
				list($time_part, $am_pm_part) = explode(' ', $input_date);
				list($input_hour, $input_minute) = explode(':', $time_part);
				$input_second = '00';
				$am_pm_part = strtolower($am_pm_part);
				if ($am_pm_part == 'pm')
				{
					if (is_numeric($input_hour))
					{
						if ($input_hour <= 11)
						{
							$input_hour += 12;
						}
					}
				}
				break;

			case "hh:mm am": // am/pm optional
				$input_date = trim($input_date);
				list($time_part, $am_pm_part) = explode(' ', $input_date);
				list($input_hour, $input_minute) = explode(':', $time_part);
				$input_second = '00';
				$am_pm_part = strtolower($am_pm_part);
				if ($am_pm_part == 'pm')
				{
					if (is_numeric($input_hour))
					{
						if ($input_hour <= 11)
						{
							$input_hour += 12;
						}
					}
				}
				break;

			case "yyyy-mm-ddThh:mm:ssZ":
				$input_year = intval(substr($input_date, 0, 4));
				$input_month = intval(substr($input_date, 5, 2));
				$input_day = intval(substr($input_date, 8, 2));
				$input_hour = intval(substr($input_date, 11, 2));
				$input_minute = intval(substr($input_date, 14, 2));
				$input_second = intval(substr($input_date, 17, 2));
				break;

			case "Dow Mon dd hh:mm:ss yyyy":
				list($input_dow, $input_month, $input_day, $input_time, $input_year) = explode(" ", $input_date);
				$input_year = intval($input_year);
				$input_month = self::get_numeric_month_from_abbr($input_month);
				$input_day = intval($input_day);
				$input_hour = intval(substr($input_time, 0, 2));
				$input_minute = intval(substr($input_time, 3, 2));
				$input_second = intval(substr($input_time, 6, 2));
				break;

			case "Dow dd Mon yyyy hh:mm:ss +0000":
				list($input_dow, $input_day, $input_month, $input_year, $input_time, $input_offset) = explode(" ", $input_date);
				$input_year = intval($input_year);
				$input_month = self::get_numeric_month_from_abbr($input_month);
				$input_day = intval($input_day);
				$input_hour = intval(substr($input_time, 0, 2));
				$input_minute = intval(substr($input_time, 3, 2));
				$input_second = intval(substr($input_time, 6, 2));
				break;

			case "yyyy-mm-ddThh:mm:ss.sss-offset":
				$input_year = intval(substr($input_date, 0, 4));
				$input_month = intval(substr($input_date, 5, 2));
				$input_day = intval(substr($input_date, 8, 2));
				$input_hour = intval(substr($input_date, 11, 2));
				$input_minute = intval(substr($input_date, 14, 2));
				$input_second = intval(substr($input_date, 17, 2));
				break;

			case "yyyy-mm-ddThh:mm:ss":
				$input_year = intval(substr($input_date, 0, 4));
				$input_month = intval(substr($input_date, 5, 2));
				$input_day = intval(substr($input_date, 8, 2));
				$input_hour = intval(substr($input_date, 11, 2));
				$input_minute = intval(substr($input_date, 14, 2));
				$input_second = intval(substr($input_date, 17, 2));
				break;

		}

		switch ($output_format)
		{
			case "yyyy-mm-dd":
				$output_date = str_pad($input_year, 4, "0", STR_PAD_LEFT)."-";
				$output_date .= str_pad($input_month, 2, "0", STR_PAD_LEFT)."-";
				$output_date .= str_pad($input_day, 2, "0", STR_PAD_LEFT);
				break;

			case "yyyy-mm-ddThh:mm:ss.000":
				$output_date  = $input_year.'-';
				$output_date .= str_pad($input_month, 2, "0", STR_PAD_LEFT)."-";
				$output_date .= str_pad($input_day, 2, "0", STR_PAD_LEFT)."T";
				$output_date .= str_pad($input_hour, 2, "0", STR_PAD_LEFT).":";
				$output_date .= str_pad($input_minute, 2, "0", STR_PAD_LEFT).":";
				$output_date .= str_pad($input_second, 2, "0", STR_PAD_LEFT).".000";
				break;

			case "yyyy-mm-dd hh:mm:ss":
				$output_date = str_pad($input_year, 4, "0", STR_PAD_LEFT)."-";
				$output_date .= str_pad($input_month, 2, "0", STR_PAD_LEFT)."-";
				$output_date .= str_pad($input_day, 2, "0", STR_PAD_LEFT)." ";
				$output_date .= str_pad($input_hour, 2, "0", STR_PAD_LEFT).":";
				$output_date .= str_pad($input_minute, 2, "0", STR_PAD_LEFT).":";
				$output_date .= str_pad($input_second, 2, "0", STR_PAD_LEFT);
				break;

			case "hh:mm:ss":
				$output_date = str_pad($input_hour, 2, "0", STR_PAD_LEFT).":";
				$output_date .= str_pad($input_minute, 2, "0", STR_PAD_LEFT).":";
				$output_date .= str_pad($input_second, 2, "0", STR_PAD_LEFT);
				break;

			case "unix":
				$output_date = str_pad($input_year, 4, "0", STR_PAD_LEFT)."-";
				$output_date .= str_pad($input_month, 2, "0", STR_PAD_LEFT)."-";
				$output_date .= str_pad($input_day, 2, "0", STR_PAD_LEFT)." ";
				$output_date .= str_pad($input_hour, 2, "0", STR_PAD_LEFT).":";
				$output_date .= str_pad($input_minute, 2, "0", STR_PAD_LEFT).":";
				$output_date .= str_pad($input_second, 2, "0", STR_PAD_LEFT);
				$output_date = strtotime($output_date);
				break;

			case "mm/dd/yyyy":
				$output_date = str_pad($input_month, 2, "0", STR_PAD_LEFT)."/";
				$output_date .= str_pad($input_day, 2, "0", STR_PAD_LEFT)."/";
				$output_date .= str_pad($input_year, 4, "0", STR_PAD_LEFT);
				break;

			case "m/d/yyyy":
				$output_date = $input_month."/";
				$output_date .= $input_day."/";
				$output_date .= str_pad($input_year, 4, "0", STR_PAD_LEFT);
				break;

		}

		return $output_date;
	}

	// convert a mysql yyyy-mm-dd hh:mm:ss date to yyyy/m/d
	public static function convert_mysql_date($datetime, $dateformat="yyyy/m/d")
	{
		debug::variable($datetime, 'datetime');
		debug::variable($dateformat, 'dateformat');

		$strDate = "";
		if (strlen($datetime) > 0)
		{
			switch ($dateformat)
			{
				case "yyyy/m/d":
					$strDate = intval(substr($datetime,0,4))."/".intval(substr($datetime,5,2))."/".intval(substr($datetime,8,2));
					break;
				case "yyyy/mm/dd":
					$strDate = substr($datetime,0,4)."/".substr($datetime,5,2)."/".substr($datetime,8,2);
					break;
				case "yyyy-mm-dd":
					$strDate = substr($datetime,0,4)."-".substr($datetime,5,2)."-".substr($datetime,8,2);
					break;
				case "mm/dd/yyyy":
					$strDate = substr($datetime,5,2)."/".substr($datetime,8,2)."/".substr($datetime,0,4);
					break;
				case "mmddyyyy":
					$strDate = substr($datetime,5,2).substr($datetime,8,2).substr($datetime,0,4);
					break;
				case "m/d/yyyy":
					$strDate = intval(substr($datetime,5,2))."/".intval(substr($datetime,8,2))."/".intval(substr($datetime,0,4));
					break;
				case "m/d/yy":
					$strDate = intval(substr($datetime,5,2))."/".intval(substr($datetime,8,2))."/".intval(substr($datetime,2,2));
					break;
				case "mm/dd/yy":
					$strDate = substr($datetime,5,2)."/".substr($datetime,8,2)."/".substr($datetime,2,2);
					break;
				case "RFC822":
					$strDate = date("r", mktime("0","0","0",substr($datetime,5,2), substr($datetime,8,2), substr($datetime,0,4)));
					break;
				case "mm/dd/yyyy hh:mm:ss am":
					$strDate  = substr($datetime,5,2)."/";
					$strDate .= substr($datetime,8,2)."/";
					$strDate .= substr($datetime,0,4)." ";
					$hour = substr($datetime,11,2);
					$am_or_pm = 'AM';
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
						$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
						$am_or_pm = 'PM';
					}
					$strDate .= $hour.":";
					$strDate .= substr($datetime,14,2).":";
					$strDate .= substr($datetime,17,2)." ";
					$strDate .= $am_or_pm;
					break;
				case "unix":
					$strDate = strtotime($datetime);
					break;
				case "Dow, Month dd, yyyy":
				  $datetime = strtotime($datetime);
				  $strDate = date("D\, F d Y", $datetime);
				  break;
				case "Dow, Month d":
					$unix_date = self::convert_mysql_date($datetime, 'unix');
					//debug::variable($unix_date, 'unix_date');
					$day_of_week = self::day_of_week($unix_date);
					//debug::variable($day_of_week, 'day_of_week');
					$month = self::month_name($unix_date, false);
					//debug::variable($month, 'month');
					$day_of_month = self::mday($unix_date);
					//debug::variable($day_of_month, 'day_of_month');
					$strDate  = $day_of_week.', '.$month.' '.$day_of_month;
					//debug::variable($strDate, 'strDate');
					break;
				  break;
				case "yyyy-mm-ddThh:mm:ss.000":
					$strDate  = substr($datetime,0,4).'-';
					$strDate .= substr($datetime,5,2)."-";
					$strDate .= substr($datetime,8,2)."T";
					$strDate .= str_pad(substr($datetime,11,2), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(substr($datetime,14,2), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(substr($datetime,17,2), 2, "0", STR_PAD_LEFT).".000";
					break;
				case "mm/dd/yyyy hh:mm:ss":
					$strDate  = substr($datetime,5,2)."/";
					$strDate .= substr($datetime,8,2)."/";
					$strDate .= substr($datetime,0,4)." ";
					$strDate .= substr($datetime,11,2).":";
					$strDate .= substr($datetime,14,2).":";
					$strDate .= substr($datetime,17,2)." ";
					break;
				case "hh:mm:ss am":
					$hour = substr($datetime,11,2);
					$am_or_pm = 'AM';
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
						$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
						$am_or_pm = 'PM';
					}
					$strDate .= $hour.":";
					$strDate .= substr($datetime,14,2).":";
					$strDate .= substr($datetime,17,2)." ";
					$strDate .= $am_or_pm;
					break;
				case "mm/dd/yyyy h:mm am":
					$strDate  = substr($datetime,5,2)."/";
					$strDate .= substr($datetime,8,2)."/";
					$strDate .= substr($datetime,0,4)." ";
					$hour = substr($datetime,11,2);
					$hour = (int) $hour;
					$am_or_pm = 'AM';
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
						$am_or_pm = 'PM';
					}
					$strDate .= $hour.":";
					$strDate .= substr($datetime,14,2)." ";
					$strDate .= $am_or_pm;
					break;

				case "h:mm am":
					$hour = substr($datetime,11,2);
					$hour = (int) $hour;
					$am_or_pm = 'AM';
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
						$am_or_pm = 'PM';
					}
					$strDate .= $hour.":";
					$strDate .= substr($datetime,14,2)." ";
					$strDate .= $am_or_pm;
					break;
				case "Mon d, yyyy":
					$month = intval(substr($datetime,5,2));
					$strDate  = self::month_name($month, true, true).' ';
					$strDate .= intval(substr($datetime,8,2)).", ";
					$strDate .= substr($datetime,0,4);
					break;
				case "Month d, yyyy":
					$month = intval(substr($datetime,5,2));
					$strDate  = self::month_name($month, false, true).' ';
					$strDate .= intval(substr($datetime,8,2)).", ";
					$strDate .= substr($datetime,0,4);
					break;
				case "yyyymmddThhmmssZ":
					$strDate  = substr($datetime,0,4).'';
					$strDate .= substr($datetime,5,2)."";
					$strDate .= substr($datetime,8,2)."T";
					$strDate .= str_pad(substr($datetime,11,2), 2, "0", STR_PAD_LEFT)."";
					$strDate .= str_pad(substr($datetime,14,2), 2, "0", STR_PAD_LEFT)."";
					$strDate .= str_pad(substr($datetime,17,2), 2, "0", STR_PAD_LEFT)."Z";
					break;
				case "yyyymmddThhmmss":
					$strDate  = substr($datetime,0,4).'';
					$strDate .= substr($datetime,5,2)."";
					$strDate .= substr($datetime,8,2)."T";
					$strDate .= str_pad(substr($datetime,11,2), 2, "0", STR_PAD_LEFT)."";
					$strDate .= str_pad(substr($datetime,14,2), 2, "0", STR_PAD_LEFT)."";
					$strDate .= str_pad(substr($datetime,17,2), 2, "0", STR_PAD_LEFT)."";
					break;
				case "m/d/yy h:mm am":
					$month = (int) substr($datetime,5,2);
					//debug::variable($month, 'month');
					$day = (int) substr($datetime,8,2);
					//debug::variable($day, 'day');
					$year = substr($datetime,2,2);
					//debug::variable($year, 'year');
					$hour = (int) substr($datetime,11,2);
					$minute = substr($datetime,14,2);
					$am = 'am';
					if ($hour >= 12)
					{
						$am = 'pm';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
					}
					$strDate  = $month."/";
					$strDate .= $day."/";
					$strDate .= $year." ";
					$strDate .= $hour.":";
					$strDate .= $minute." ";
					$strDate .= $am;
					break;
				case "d/Mon/yy h:mm am":
					$month = (int) substr($datetime,5,2);
					//debug::variable($month, 'month');
					$day = (int) substr($datetime,8,2);
					//debug::variable($day, 'day');
					$year = substr($datetime,2,2);
					//debug::variable($year, 'year');
					$hour = (int) substr($datetime,11,2);
					$minute = substr($datetime,14,2);
					$am = 'am';
					if ($hour >= 12)
					{
						$am = 'pm';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
					}
					$mon = self::display_month_abbr($month);
					$strDate = $day."/";
					$strDate .= $mon."/";
					$strDate .= $year." ";
					$strDate .= $hour.":";
					$strDate .= $minute." ";
					$strDate .= $am;
					break;
				case "d/Mon/yy":
					$month = (int) substr($datetime,5,2);
					$day = (int) substr($datetime,8,2);
					$year = substr($datetime,2,2);
					$mon = self::display_month_abbr($month);
					$strDate = $day."/";
					$strDate .= $mon."/";
					$strDate .= $year;
					break;
				case "yyyy-mm-dd hh:mm:ss":
					$strDate  = substr($datetime,0,4).'-';
					$strDate .= substr($datetime,5,2)."-";
					$strDate .= substr($datetime,8,2)." ";
					$strDate .= str_pad(substr($datetime,11,2), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(substr($datetime,14,2), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(substr($datetime,17,2), 2, "0", STR_PAD_LEFT);
					break;
			}
		}
		return $strDate;
	}

	public static function convert_mysql_time($time, $timeformat="h:mm am")
	{
		debug::variable($time, 'time');
		debug::variable($timeformat, 'timeformat');

		$strTime = "";
		if (strlen($time) > 0)
		{
			switch ($timeformat)
			{
				case "hh:mm:ss am":
					$hour = substr($time,0,2);
					$am_or_pm = 'AM';
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
						$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
						$am_or_pm = 'PM';
					}
					$strTime .= $hour.":";
					$strTime .= substr($time,3,2).":";
					$strTime .= substr($time,6,2)." ";
					$strTime .= $am_or_pm;
					break;
				case "h:mm am":
					$hour = substr($time,0,2);
					$hour = (int) $hour;
					$am_or_pm = 'AM';
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
						$am_or_pm = 'PM';
					}
					$strTime .= $hour.":";
					$strTime .= substr($time,3,2).' ';
					$strTime .= $am_or_pm;
					break;

			}
		}
		return $strTime;
	}

	// convert unix date
	public static function convert_unix_date($unixdate, $dateformat="yyyy-mm-dd hh:mm:ss", $now=false)
	{
		debug::on(false);

		$strDate = "";
		if ($now)
		{
			$unixdate = time();
		}
		if (strlen($unixdate) > 0)
		{
			switch ($dateformat)
			{
				case "yyyy-mm-dd hh:mm:ss":
					$strDate = self::year($unixdate)."-";
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)." ";
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;
				case "yyyy-mm-ddthh:mm:ss":
					$strDate = self::year($unixdate)."-";
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)."T";
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;
				case "yyyy-mm-dd hh:mm":
					$strDate = self::year($unixdate)."-";
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)." ";
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT);
					break;
				case "yyyy-mm-dd":
					$strDate = self::year($unixdate)."-";
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT);
					break;
				case "yyyymmdd":
					$strDate = self::year($unixdate);
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT);
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT);
					break;
				case "mm/dd/yyyy hh:mm:ss am":
					$strDate  = str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."/";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)."/";
					$strDate .= self::year($unixdate)." ";
					$strDate .= str_pad(self::hour($unixdate, false), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT)." ";
					$strDate .= ((self::is_am($unixdate)) ? 'AM' : 'PM' );
					break;

				case "mm/dd/yyyy hh:mm:ss":
					$strDate  = str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."/";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)."/";
					$strDate .= self::year($unixdate)." ";
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;

				case "hh:mm:ss":
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;

				case "hhmmss":
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT);
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT);
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;

				case "hhmm":
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT);
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT);
					break;

				case "hh:mm:ss am":
					$hour = str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$am_or_pm = 'AM';
					if ($hour >= 12)
					{
						$am_or_pm = 'PM';
					}
					if ($hour > 12)
					{
						$hour = $hour - 12;
						$hour = str_pad($hour, 2, "0", STR_PAD_LEFT);
						$am_or_pm = 'PM';
					}
					$strDate .= $hour;
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT)." ";
					$strDate .= $am_or_pm;
					break;

				case "mm/dd/yyyy":
					$strDate  = str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."/";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)."/";
					$strDate .= self::year($unixdate);
					break;

				case "Mon d, yyyy":
					$strDate  = self::month_name($unixdate).' ';
					$strDate .= self::mday($unixdate).", ";
					$strDate .= self::year($unixdate);
					break;

				case "Mon d":
					$strDate  = self::month_name($unixdate).' ';
					$strDate .= self::mday($unixdate);
					break;

				case "yyyy-mm-dd-hh-mm-ss":
					$strDate = self::year($unixdate)."-";
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;

				case "yyyymmddhhmmss":
					$strDate = self::year($unixdate);
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT);
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT);
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT);
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT);
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT);
					break;

				case "yyyy-mm-ddThh:mm:ss-offset":
					$strDate = self::year($unixdate)."-";
					$strDate .= str_pad(self::mon($unixdate), 2, "0", STR_PAD_LEFT)."-";
					$strDate .= str_pad(self::mday($unixdate), 2, "0", STR_PAD_LEFT)."T";
					$strDate .= str_pad(self::hour($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::minute($unixdate), 2, "0", STR_PAD_LEFT).":";
					$strDate .= str_pad(self::second($unixdate), 2, "0", STR_PAD_LEFT).'';

					//$offset = '-04:00'; // eastern time

					$gmt_offset = self::get_local_gmt_offset();
					debug::variable($gmt_offset);

					$is_negative = ($gmt_offset < 0);
					debug::variable($is_negative);

					$offset = '';
					if ($is_negative)
					{
						$offset .= '-';
						debug::variable($offset);

						$gmt_offset = $gmt_offset * (-1);
						debug::variable($gmt_offset);
					}
					else
					{
						$offset .= '+';
						debug::variable($offset);
					}

					$offset .= str_pad($gmt_offset, 2, '0', STR_PAD_LEFT);
					$offset .= ':00';
					debug::variable($offset);

					$strDate .= $offset;
					debug::variable($strDate);
					break;
			}
		}
		return $strDate;
	}

	// Note: on May 31, if you ask for 1 month ago, you will get 4/31, which will resolve to 5/1.
	// March 29,30,31, July 31, Oct 31, and Dec 31, all have the same issue.
	// Note: on leap year, 2/29, if you ask for 1 year ago, you will get 2/29, which will resolve to 3/1.
	public static function DateAdd($interval, $number, $date)
	{

		// Usage:
		// DateAdd (inteval, number, date)
		//	 interval:
		//		y		year
		//		m		Month
		//		d		Day
		//		w		Weeks
		//		h		Hour
		//		n		Minute
		//		s		Second
		//	number:
		//		unit quantity to add to starting date
		//	date:
		//		starting date

		$date_time_array  = getdate($date);

		$hours =  $date_time_array["hours"];
		$minutes =  $date_time_array["minutes"];
		$seconds =  $date_time_array["seconds"];
		$month =  $date_time_array["mon"];
		$day =  $date_time_array["mday"];
		$year =  $date_time_array["year"];

		switch ($interval)
		{
			case "y":
				$year +=$number;
				break;
			case "m":
				$month +=$number;
				break;
			case "d":
				$day+=$number;
				break;
			case "w":
				$day+=($number*7);
				break;
			case "h":
				$hours+=$number;
				break;
			case "n":
				$minutes+=$number;
				break;
			case "s":
				$seconds+=$number;
				break;

		}
		$timestamp = mktime($hours ,$minutes, $seconds, $month ,$day, $year);
		return $timestamp;
	}



	// daylight savings time will cause issues here
	// the difference between two day dates could be +/- 1 hour
	public static function DateDiff($interval, $date1, $date2)
	{

		// Usage:
		// DateDiff (inteval, date1, date2)
		//	 interval:
		//		w		Weeks
		//		d		Days
		//		h		Hours
		//		n		Minutes
		//		s		Seconds
		//	date1:
		//		starting date
		//	date2:
		//		ending date


		// get the number of seconds between the two dates
		$timedifference = $date2 - $date1;

		switch ($interval)
		{
			case "w":
				//$retval = bcdiv($timedifference ,604800);
				$retval = $timedifference / 604800;
				break;
			case "d":
				//$retval = bcdiv( $timedifference,86400);
				$retval = $timedifference / 86400;
				break;
			case "h":
				//$retval = bcdiv ($timedifference,3600);
				$retval = $timedifference / 3600;
				break;
			case "n":
				//$retval = bcdiv( $timedifference,60);
				$retval = $timedifference / 60;
				break;
			case "s":
				$retval = $timedifference;
				break;
		}
		return $retval;

	}

	public static function day_of_month_number_extension($day_of_month)
	{
		switch ($day_of_month)
		{
			case 1: $return = 'st'; break;
			case 2: $return = 'nd'; break;
			case 3: $return = 'rd'; break;
			case 4: $return = 'th'; break;
			case 5: $return = 'th'; break;
			case 6: $return = 'th'; break;
			case 7: $return = 'th'; break;
			case 8: $return = 'th'; break;
			case 9: $return = 'th'; break;
			case 10: $return = 'th'; break;
			case 11: $return = 'th'; break;
			case 12: $return = 'th'; break;
			case 13: $return = 'th'; break;
			case 14: $return = 'th'; break;
			case 15: $return = 'th'; break;
			case 16: $return = 'th'; break;
			case 17: $return = 'th'; break;
			case 18: $return = 'th'; break;
			case 19: $return = 'th'; break;
			case 21: $return = 'st'; break;
			case 22: $return = 'nd'; break;
			case 23: $return = 'rd'; break;
			case 24: $return = 'th'; break;
			case 25: $return = 'th'; break;
			case 26: $return = 'th'; break;
			case 27: $return = 'th'; break;
			case 28: $return = 'th'; break;
			case 29: $return = 'th'; break;
			case 30: $return = 'th'; break;
			case 31: $return = 'st'; break;
		}
		return $return;
	}

	public static function display_date($datetime=0)
	{
		if ($datetime == 0)
		{
			$datetime = time();
		}
		return date("m/d/Y", $datetime);
	}

	public static function display_datetime($datetime=0)
	{
		if ($datetime == 0)
		{
			$datetime = time();
		}
		return date("m/d/Y H:i:s", $datetime);
	}

	public static function display_month($numeric_month)
	{
		switch ($numeric_month) {
			case 1:
				return 'January';
				break;
			case 2:
				return 'February';
				break;
			case 3:
				return 'March';
				break;
			case 4:
				return 'April';
				break;
			case 5:
				return 'May';
				break;
			case 6:
				return 'June';
				break;
			case 7:
				return 'July';
				break;
			case 8:
				return 'August';
				break;
			case 9:
				return 'September';
				break;
			case 10:
				return 'October';
				break;
			case 11:
				return 'November';
				break;
			case 12:
				return 'December';
				break;
		}
	}


	public static function display_month_abbr($numeric_month)
	{
		switch ($numeric_month) {
			case 1:
				return 'Jan';
				break;
			case 2:
				return 'Feb';
				break;
			case 3:
				return 'Mar';
				break;
			case 4:
				return 'Apr';
				break;
			case 5:
				return 'May';
				break;
			case 6:
				return 'Jun';
				break;
			case 7:
				return 'Jul';
				break;
			case 8:
				return 'Aug';
				break;
			case 9:
				return 'Sep';
				break;
			case 10:
				return 'Oct';
				break;
			case 11:
				return 'Nov';
				break;
			case 12:
				return 'Dec';
				break;
		}
	}

	public static function display_mysql_date($datetime)
	{
	  $datetime = strtotime($datetime);
	  return date("m/d/Y", $datetime);
	}


	public static function friendly_time_length($seconds, $decimals=1)
	{
		$minutes = $seconds / 60;
		$hours = $minutes / 60;
		$days = $hours / 24;

		debug::variable($seconds, 'seconds');
		debug::variable($minutes, 'minutes');
		debug::variable($hours, 'hours');
		debug::variable($days, 'days');

		if ($seconds < 60)
		{
			return number_format($seconds, $decimals, '.', ',')." sec";
		}
		elseif ($minutes < 60)
		{
			return number_format($minutes, $decimals, '.', ',')." min";
		}
		elseif ($hours < 24)
		{
			return number_format($hours, $decimals, '.', ',')." hr";
		}
		else
		{
			return number_format($days, $decimals, '.', ',')." day";
		}
	}

	public static function get_numeric_month_from_abbr($text_month)
	{
		$text_month = strtolower($text_month);
		switch ($text_month)
		{
			case 'jan':
				return 1;
				break;
			case 'feb':
				return 2;
				break;
			case 'mar':
				return 3;
				break;
			case 'apr':
				return 4;
				break;
			case 'may':
				return 5;
				break;
			case 'jun':
				return 6;
				break;
			case 'jly':
				return 7;
				break;
			case 'jul':
				return 7;
				break;
			case 'aug':
				return 8;
				break;
			case 'sep':
				return 9;
				break;
			case 'oct':
				return 10;
				break;
			case 'nov':
				return 11;
				break;
			case 'dec':
				return 12;
				break;
		}
	}

	public static function get_numeric_month_from_name($text_month)
	{
		$text_month = strtolower($text_month);
		switch ($text_month)
		{
			case 'january':
				return 1;
				break;
			case 'february':
				return 2;
				break;
			case 'march':
				return 3;
				break;
			case 'april':
				return 4;
				break;
			case 'may':
				return 5;
				break;
			case 'june':
				return 6;
				break;
			case 'july':
				return 7;
				break;
			case 'august':
				return 8;
				break;
			case 'september':
				return 9;
				break;
			case 'october':
				return 10;
				break;
			case 'november':
				return 11;
				break;
			case 'december':
				return 12;
				break;
		}
	}


	public static function hour($datetime=0)
	{
		// Returns the numeric hour
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['hours'];
	}

	public static function is_am($datetime=0)
	{
		// Returns the numeric hour
		if ($datetime == 0)
		{
			$datetime = time();
		}
		if (self::hour($datetime) < 12)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function is_date_set($datetime)
	{
		$isdate = true;
		if (strlen($datetime) == 0) {
			$isdate = false;
		}
		if (is_null($datetime)) {
			$isdate = false;
		}
		if ($datetime == "0") {
			$isdate = false;
		}
		if ($datetime == "0000-00-00") {
			$isdate = false;
		}
		return $isdate;
	}

	public static function is_friday($datetime=0)
	{
		// Returns true or false
		if ($datetime == 0)
		{
			$datetime = time();
		}
		$weekday = self::wday($datetime);
		if ($weekday == 5)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function is_monday($datetime=0)
	{
		// Returns true or false
		if ($datetime == 0)
		{
			$datetime = time();
		}
		$weekday = self::wday($datetime);
		if ($weekday == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function is_mysql_date_format($datetime)
	{
		//yyyy-mm-dd hh:mm:ss
		//yyyy
		if (!is_numeric(substr($datetime, 0, 4)))
		{
			return false;
		}
		//-
		if (substr($datetime, 4, 1) <> '-')
		{
			return false;
		}
		//mm
		if (!is_numeric(substr($datetime, 5, 2)))
		{
			return false;
		}
		//-
		if (substr($datetime, 7, 1) <> '-')
		{
			return false;
		}
		//dd
		if (!is_numeric(substr($datetime, 8, 2)))
		{
			return false;
		}
		if (strlen($datetime) > 10)
		{
			// space
			if (substr($datetime, 10, 1) <> ' ')
			{
				return false;
			}
			// hh
			if (!is_numeric(substr($datetime, 11, 2)))
			{
				return false;
			}
			// :
			if (substr($datetime, 13, 1) <> ':')
			{
				return false;
			}
			// mm
			if (!is_numeric(substr($datetime, 14, 2)))
			{
				return false;
			}
			// :
			if (substr($datetime, 16, 1) <> ':')
			{
				return false;
			}
			// ss
			if (!is_numeric(substr($datetime, 17, 2)))
			{
				return false;
			}
		}
		return true;
	}

	public static function is_pm($datetime=0)
	{
		// Returns the numeric hour
		if ($datetime == 0)
		{
			$datetime = time();
		}
		if (self::hour($datetime) > 11)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function is_thursday($datetime=0)
	{
		// Returns true or false
		if ($datetime == 0)
		{
			$datetime = time();
		}
		$weekday = self::wday($datetime);
		if ($weekday == 4)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function is_tuesday($datetime=0)
	{
		// Returns true or false
		if ($datetime == 0)
		{
			$datetime = time();
		}
		$weekday = self::wday($datetime);
		if ($weekday == 2)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function is_saturday($datetime=0)
	{
		// Returns true or false
		if ($datetime == 0)
		{
			$datetime = time();
		}
		$weekday = self::wday($datetime);
		if ($weekday == 6)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function is_sunday($datetime=0)
	{
		// Returns true or false
		if ($datetime == 0)
		{
			$datetime = time();
		}
		$weekday = self::wday($datetime);
		if ($weekday == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function is_wednesday($datetime=0)
	{
		// Returns true or false
		if ($datetime == 0)
		{
			$datetime = time();
		}
		$weekday = self::wday($datetime);
		if ($weekday == 3)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function last_day_of_month($datetime=0)
	{
		// Returns the number of days in the month
		if ($datetime == 0)
		{
			$datetime = time();
		}
		if (self::mon($datetime) == 12)
		{
			$last_day = self::mday(mktime (0, 0, 0, 1, 0, self::year($datetime) + 1));
		}
		else
		{
			$last_day = self::mday(mktime (0, 0, 0, self::mon($datetime) + 1, 0, self::year($datetime)));
		}
		return $last_day;
	}

	public static function log_timestamp()
	{
		$unix_time = time();

		$year = self::year($unix_time);
		$mon = self::mon($unix_time);
		$mday = self::mday($unix_time);
		$hour = self::hour($unix_time);
		$minute = self::minute($unix_time);
		$second = self::second($unix_time);

		return
			str_pad($year, 4, "0", STR_PAD_LEFT).
			str_pad($mon, 2, "0", STR_PAD_LEFT).
			str_pad($mday, 2, "0", STR_PAD_LEFT).
			str_pad($hour, 2, "0", STR_PAD_LEFT).
			str_pad($minute, 2, "0", STR_PAD_LEFT).
			str_pad($second, 2, "0", STR_PAD_LEFT);
	}

	public static function mday($datetime=0)
	{
		// Returns the numeric day of month
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['mday'];
	}

	// $start_time = get_micro_time();
	// $end_time = get_micro_time();
	// echo display_micro_time_diff($start_time, $end_time);
	public static function micro_time()
	{
		list($sec, $usec) = explode(" ", microtime());
		return ($sec + $usec);
	}

	public static function micro_time_diff($start_time, $end_time)
	{
		$diff = $end_time - $start_time;
		if ($diff < 0.0001)
		{
			return "0.0000";
		}
		else
		{
			return substr(($diff), 0, 6);
		}
	}

	public static function minute($datetime=0)
	{
		// Returns the numeric minute
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['minutes'];
	}

	public static function mon($datetime=0)
	{
		// Returns the numeric month
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['mon'];
	}

	public static function month($datetime=0)
	{
		// Returns the textual month
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['month'];
	}

	public static function month_name($datetime=0, $abbreviated=true, $numeric_month_input=false)
	{
		debug::on(false);
		debug::variable($datetime, 'datetime');
		debug::variable($abbreviated, 'abbreviated');
		debug::variable($numeric_month_input, 'numeric_month_input');

		// Returns the name of the month
		if ($datetime == 0)
		{
			$datetime = time();
			debug::variable($datetime, 'datetime');
		}
		$date_array = getdate($datetime);
		debug::variable($date_array, 'date_array');
		if ($numeric_month_input)
		{
			$month_number = $datetime;
			debug::variable($month_number, 'month_number');
		}
		else
		{
			$month_number = $date_array['mon'];
			debug::variable($month_number, 'month_number');
		}
		if ($abbreviated)
		{
			switch ($month_number)
			{
				case 1:
					return 'Jan';
					break;
				case 2:
					return 'Feb';
					break;
				case 3:
					return 'Mar';
					break;
				case 4:
					return 'Apr';
					break;
				case 5:
					return 'May';
					break;
				case 6:
					return 'Jun';
					break;
				case 7:
					return 'Jly';
					break;
				case 8:
					return 'Aug';
					break;
				case 9:
					return 'Sep';
					break;
				case 10:
					return 'Oct';
					break;
				case 11:
					return 'Nov';
					break;
				case 12:
					return 'Dec';
					break;
			}
		}
		else // not abbreviated
		{
			switch ($month_number)
			{
				case 1:
					return 'January';
					break;
				case 2:
					return 'February';
					break;
				case 3:
					return 'March';
					break;
				case 4:
					return 'April';
					break;
				case 5:
					return 'May';
					break;
				case 6:
					return 'June';
					break;
				case 7:
					return 'July';
					break;
				case 8:
					return 'August';
					break;
				case 9:
					return 'September';
					break;
				case 10:
					return 'October';
					break;
				case 11:
					return 'November';
					break;
				case 12:
					return 'December';
					break;
			}
		}

	}

	public static function mysql_now($unix_now="", $output_format="yyyy-mm-dd hh:mm:ss")
	{
		if (empty($unix_now))
		{
			$unix_now = time();
		}
		debug::variable($unix_now, 'unix_now');

		$year = self::year($unix_now);
		debug::variable($year, 'year');

		$mon = self::mon($unix_now);
		debug::variable($mon, 'mon');

		$mday = self::mday($unix_now);
		debug::variable($mday, 'mday');

		if ($output_format == 'yyyy-mm-dd')
		{
			$mysql_now = $year.'-'.str_pad($mon, 2, '0', STR_PAD_LEFT).'-'.str_pad($mday, 2, '0', STR_PAD_LEFT);
		}
		else
		{
			$hour = self::hour($unix_now);
			debug::variable($hour, 'hour');

			$minute = self::minute($unix_now);
			debug::variable($minute, 'minute');

			$second = self::second($unix_now);
			debug::variable($second, 'second');

			$mysql_now = $year.'-'.str_pad($mon, 2, '0', STR_PAD_LEFT).'-'.str_pad($mday, 2, '0', STR_PAD_LEFT).' '.str_pad($hour, 2, '0', STR_PAD_LEFT).':'.str_pad($minute, 2, '0', STR_PAD_LEFT).':'.str_pad($second, 2, '0', STR_PAD_LEFT);
		}

		debug::variable($mysql_now, 'mysql_now');

		return $mysql_now;
	}

	public static function second($datetime=0)
	{
		// Returns the numeric second
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['seconds'];
	}

	public static function today($datetime=0)
	{
		// Returns todays date at 12:00:00 am
		if ($datetime == 0) { $datetime = time(); }
		$today = mktime(0, 0, 0, self::mon(), self::mday(), self::year());
		return $today;
	}

	public static function wday($datetime=0)
	{
		// Returns the numeric day of week
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['wday'];
	}

	public static function weekday($datetime=0)
	{
		// Returns the textual weekday
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['weekday'];
	}


	public static function yday($datetime=0)
	{
		// Returns the numeric day of year
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['yday'];
	}


	public static function year($datetime=0) {
		// Returns the numeric year
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['year'];
	}

	public static function get_local_timezone()
	{
		return date_default_timezone_get();
	}

	public static function set_local_timezone($timezone)
	{
		return date_default_timezone_set($timezone);
	}

	public static function get_local_gmt_offset()
	{
		debug::on(false);

		$local_timezone = self::get_local_timezone();
		debug::variable($local_timezone, 'local_timezone');

		$local_dtz = new DateTimeZone($local_timezone);
		debug::variable($local_dtz, 'local_dtz');

		$zero_dtz = new DateTimeZone('GMT+0');
		debug::variable($zero_dtz, 'zero_dtz');

		$local_time = new DateTime("now", $local_dtz);
		debug::variable($local_time, 'local_time');

		$zero_time = new DateTime("now", $zero_dtz);
		debug::variable($zero_time, 'zero_time');

		// number of seconds
		$gmt_offset = $local_dtz->getOffset($local_time) - $zero_dtz->getOffset($zero_time);
		debug::variable($gmt_offset, 'gmt_offset');

		// change to hours
		$gmt_offset = $gmt_offset / 60 / 60;

		return $gmt_offset;
	}

	public static function adjust_gmt_zero_time_to_local($unix_time)
	{
		debug::on(false);

		$gmt_offset = self::get_local_gmt_offset();
		debug::variable($gmt_offset, 'gmt_offset');

		$local_time = self::DateAdd('h', $gmt_offset, $unix_time);
		debug::variable($local_time, 'local_time');

		return $local_time;
	}

	public static function start_of_day($unix_time)
	{
		$new_date = self::year($unix_time).'-'.self::mon($unix_time).'-'.self::mday($unix_time).' 00:00:00';
		return strtotime($new_date);
	}

	public static function end_of_day($unix_time)
	{
		$new_date = self::year($unix_time).'-'.self::mon($unix_time).'-'.self::mday($unix_time).' 23:59:59';
		return strtotime($new_date);
	}

	public static function day_of_week($unix_time, $is_full_name=true)
	{
		//debug::on(false);
		//debug::variable($unix_time, 'unix_time');
		//debug::variable($is_full_name, 'is_full_name');

		$week_day = self::wday($unix_time);
		//debug::variable($week_day, 'week_day');

		if ($is_full_name)
		{
			switch ($week_day)
			{
				case 0:
					return 'Sunday';
					break;
				case 1:
					return 'Monday';
					break;
				case 2:
					return 'Tuesday';
					break;
				case 3:
					return 'Wednesday';
					break;
				case 4:
					return 'Thursday';
					break;
				case 5:
					return 'Friday';
					break;
				case 6:
					return 'Saturday';
					break;
			}
		}
		else
		{
			switch ($week_day)
			{
				case 0:
					return 'Sun';
					break;
				case 1:
					return 'Mon';
					break;
				case 2:
					return 'Tue';
					break;
				case 3:
					return 'Wed';
					break;
				case 4:
					return 'Thu';
					break;
				case 5:
					return 'Fri';
					break;
				case 6:
					return 'Sat';
					break;
			}
		}

	}

	public static function display_small_month_calendar($month, $year, $day_colors, $prev_month_link='', $next_month_link='')
	{
		debug::on(false);
		debug::string("display_small_month_calendar()");
		debug::variable($month, 'month');
		debug::variable($year, 'year');
		debug::variable($day_colors, 'day_colors');
		debug::variable($prev_month_link, 'prev_month_link');
		debug::variable($next_month_link, 'next_month_link');

		$month_name = self::display_month($month);
		debug::variable($month_name, 'month_name');

		$unix_month = strtotime($month.'/'.'1'.'/'.$year.' 00:00:00');
		debug::variable($unix_month, 'unix_month');

		$mysql_month = self::convert_unix_date($unix_month, 'yyyy-mm-dd');
		debug::variable($mysql_month, 'mysql_month');

		// sun = 0
		$first_weekday_of_month = self::wday($unix_month);
		debug::variable($first_weekday_of_month, 'first_weekday_of_month');

		$last_day_of_month = self::last_day_of_month($unix_month);
		debug::variable($last_day_of_month, 'last_day_of_month');

		$content = '';


		$curr_dom = 1;
		$curr_mysql_date = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($curr_dom, 2, '0', STR_PAD_LEFT);
		debug::variable($curr_mysql_date, 'curr_mysql_date');
		$curr_day_color = (isset($day_colors[$curr_mysql_date])) ? $day_colors[$curr_mysql_date] : '';

		$content .= "<table cellspacing=0 cellpadding=4 border=0>";
		$content .= "<tr><td colspan=\"7\">";
		if (!empty($prev_month_link))
		{
			$content .= "<a href=\"".$prev_month_link."\">&#9668;</a>&nbsp;";
		}
		$content .= "<span class=\"small_month_month_name\">".$month_name." ".$year."</span>";
		if (!empty($next_month_link))
		{
			$content .= "&nbsp;<a href=\"".$next_month_link."\">&#9658;</a>";
		}
		$content .= "</tr>";
		$content .= '<tr style="background-color:#f0f0f0;">';
			$content .= "<td>S</td>";
			$content .= "<td>M</td>";
			$content .= "<td>T</td>";
			$content .= "<td>W</td>";
			$content .= "<td>T</td>";
			$content .= "<td>F</td>";
			$content .= "<td>S</td>";
		$content .= "</tr>";
		while ($curr_dom < $last_day_of_month)
		{
			$content .= "<tr>";
			for ($day_col = 0; $day_col <= 6; $day_col++)
			{
				if ($curr_dom == 1)
				{
					if ($day_col == $first_weekday_of_month)
					{
						$content .= '<td style="background-color:'.$curr_day_color.'">'.$curr_dom."</td>";
						$curr_dom++;
						$curr_mysql_date = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($curr_dom, 2, '0', STR_PAD_LEFT);
						debug::variable($curr_mysql_date, 'curr_mysql_date');
						$curr_day_color = (isset($day_colors[$curr_mysql_date])) ? $day_colors[$curr_mysql_date] : '';
					}
					else
					{
						$content .= "<td>&nbsp;</td>";
					}
				}
				elseif ($curr_dom <= $last_day_of_month)
				{
					$content .= '<td style="background-color:'.$curr_day_color.'">'.$curr_dom."</td>";
					$curr_dom++;
					$curr_mysql_date = $year.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($curr_dom, 2, '0', STR_PAD_LEFT);
					debug::variable($curr_mysql_date, 'curr_mysql_date');
					$curr_day_color = (isset($day_colors[$curr_mysql_date])) ? $day_colors[$curr_mysql_date] : '';
				}
				else
				{
					$content .= "<td>&nbsp;</td>";
				}
			}
			$content .= "<tr>";
		}
		$content .= "</table>";

		return $content;
	}

	public static function unix_now($mysql_date_time_override='')
	{
		debug::on(false);
		debug::variable($mysql_date_time_override);

		if (empty($mysql_date_time_override))
		{
			$unix_now = time();
			debug::variable($unix_now);
		}
		else
		{
			$unix_now = self::convert_mysql_date($mysql_date_time_override, 'unix');
			debug::variable($unix_now);
		}
		return $unix_now;
	}
}


?>