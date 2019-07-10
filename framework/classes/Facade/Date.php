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

namespace Facade;

use debug;
use DateTimeZone;
use DateTime;

class Date
{
	public function _construct()
	{
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

	public static function second($datetime=0)
	{
		// Returns the numeric second
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['seconds'];
	}

	public static function wday($datetime=0)
	{
		// Returns the numeric day of week
		if ($datetime == 0) { $datetime = time(); }
		$date_array = getdate($datetime);
		return $date_array['wday'];
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