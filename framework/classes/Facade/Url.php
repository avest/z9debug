<?php
//===================================================================
// Z9 Framework
//===================================================================
// Url.php
// --------------------
//       Date Created: 2005-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

// function build_url($scheme, $host, $port, $path, $query, $fragment)
// function filter_query_parameters($input_url, $filters)
// function get_query_parameters($input_url)
// function safe_url($input_url)
// function url_string_to_array($url_string)

namespace Facade;

use debug;
use Facade\Str;


class Url
{
	public function _construct()
	{
	}

	public static function build_url($scheme, $host, $port, $path, $query, $fragment)
	{
		$return = '';
		if (!empty($host))
		{
			if (!empty($scheme))
			{
				$return .= $scheme."://";
			}
			else
			{
				$return .= "http://";
			}
			$return .= $host;
			if (!empty($port))
			{
				$return .= ":".$port;
			}
		}
		$return .= $path;
		if (!empty($query))
		{
			$return .= "?".$query;
		}
		if (!empty($fragment))
		{
			$return .= "#".$fragment;
		}
		return $return;
	}

	public static function get_query_parameters($input_url)
	{
		debug::on(false);
		$return_var_array = array();

		// break url into array components
		$temp_document_array = @parse_url($input_url);

		// capture query component of url
		$temp_document_query = array();
		if (isset($temp_document_array['query']))
		{
			$temp_document_query = $temp_document_array['query'];
		}

		// split query into variable components
		$query_vars = array();
		if (!empty($temp_document_query))
		{
			$query_vars = explode("&", $temp_document_query);
		}

		$new_document_query = '';

		debug::variable($query_vars, 'query_vars');
		if (is_array($query_vars))
		{
			// loop through each variable and
			// rebuild query string without filtered parameters
			foreach($query_vars as $query_var)
			{
				if (!empty($query_var))
				{
					debug::variable($query_var, 'query_var');
					// split variable into name and value
					list($var_name, $var_value) = explode("=", $query_var);
					$return_var_array[$var_name] = $var_value;
				}
			}
		}

		return $return_var_array;
	}

	public static function filter_query_parameters($input_url, $filters)
	{
		// break url into array components
		$temp_document_array = @parse_url($input_url);

		// capture query component of url
		$temp_document_query = $temp_document_array['query'];

		// split query into variable components
		$query_vars = explode("&", $temp_document_query);

		$new_document_query = '';

		if (is_array($query_vars))
		{
			// loop through each variable and
			// rebuild query string without filtered parameters
			foreach($query_vars as $query_var)
			{

				// loop through each filter
				$filter_match = false;
				foreach ($filters as $filter)
				{
					if (Str::starts_with($query_var, $filter.'='))
					{
						$filter_match = true;
					}
				}

				if (!$filter_match)
				{
					$new_document_query .= '&' . $query_var;
				}
			}
		}

		if (Str::first_char($new_document_query) == '&')
		{
			$new_document_query = Str::remove_first_char($new_document_query);
		}

		// remove trailing '&'
		if (Str::last_char($new_document_query) == '&')
		{
			$new_document_query = Str::remove_last_char($new_document_query);
		}

		$output_url = self::build_url(
			(isset($temp_document_array['scheme'])) ? $temp_document_array['scheme'] : '',
			(isset($temp_document_array['host'])) ? $temp_document_array['host'] : '',
			(isset($temp_document_array['port'])) ? $temp_document_array['port'] : '',
			(isset($temp_document_array['path'])) ? $temp_document_array['path'] : '',
			(isset($new_document_query)) ? $new_document_query : '',
			(isset($temp_document_array['fragment'])) ? $temp_document_array['fragment'] : ''
		);

		return $output_url;
	}

	// the purpose of this function is to remove any cross site script content
	// from a url.
	public static function safe_url($input_url)
	{
		debug::on(false);
		debug::string("safe_url()");

		debug::variable($input_url, 'input_url');

		$decoded_url = rawurldecode($input_url);
		debug::variable($decoded_url, 'decoded_url');

		// break url into array components
		$temp_document_array = @parse_url($input_url);
		debug::variable($temp_document_array, 'temp_document_array');

		// capture query component of url
		$temp_document_query = '';
		if (isset($temp_document_array['query']))
		{
			$temp_document_query = $temp_document_array['query'];
		}
		debug::variable($temp_document_query, 'temp_document_query');

		// split query into variable components
		$query_vars = explode("&", $temp_document_query);
		debug::variable($query_vars, 'query_vars');

		$new_document_query = '';

		if (is_array($query_vars))
		{
			// loop through each variable and
			// rebuild query string without filtered parameters
			foreach($query_vars as $query_var)
			{
				debug::variable($query_var, 'query_var');

				$filter = false;

				if (Str::in_str(strtoupper($query_var), '%3CSCRIPT%3E'))
				{
					$filter = true;
				}
				if (Str::in_str(strtoupper($query_var), '<SCRIPT>'))
				{
					$filter = true;
				}
				if (Str::in_str(strtoupper($query_var), '%3C/SCRIPT%3E'))
				{
					$filter = true;
				}
				if (Str::in_str(strtoupper($query_var), '</SCRIPT>'))
				{
					$filter = true;
				}

				if (!$filter)
				{
					$new_document_query .= '&' . $query_var;
				}
				debug::variable($new_document_query, 'new_document_query');
			}
		}

		if (Str::first_char($new_document_query) == '&')
		{
			$new_document_query = Str::remove_first_char($new_document_query);
			debug::variable($new_document_query, 'new_document_query');
		}

		// remove trailing '&'
		if (Str::last_char($new_document_query) == '&')
		{
			$new_document_query = Str::remove_last_char($new_document_query);
			debug::variable($new_document_query, 'new_document_query');
		}

		if (!isset($temp_document_array['scheme']))
		{
			$temp_document_array['scheme'] = '';
		}
		if (!isset($temp_document_array['host']))
		{
			$temp_document_array['host'] = '';
		}
		if (!isset($temp_document_array['port']))
		{
			$temp_document_array['port'] = '';
		}
		if (!isset($temp_document_array['path']))
		{
			$temp_document_array['path'] = '';
		}
		if (!isset($temp_document_array['fragment']))
		{
			$temp_document_array['fragment'] = '';
		}

		$output_url = self::build_url(
			$temp_document_array['scheme'],
			$temp_document_array['host'],
			$temp_document_array['port'],
			$temp_document_array['path'],
			$new_document_query,
			$temp_document_array['fragment']);

		return $output_url;
	}

	public static function url_string_to_array($url_string)
	{
		debug::variable($url_string, 'url_string');

		$return_array = array();

		$pair_array = '';
		if (Str::in_str($url_string, '&'))
		{
			$pair_array=explode('&', $url_string);
		}
		debug::variable($pair_array, 'pair_array');
		if (is_array($pair_array) && !empty($pair_array))
		{
			foreach($pair_array as $key => $name_value_string)
			{
				$name_value = '';
				if (Str::in_str($name_value_string, '='))
				{
					$name_value = explode('=', $name_value_string);
				}
				if (is_array($name_value) && !empty($name_value))
				{
					if (isset($name_value[0]) and isset($name_value[1]))
					{
						$return_array[$name_value[0]] = urldecode($name_value[1]);
					}
				}
			}
		}
		else
		{
			$name_value = '';
			if (Str::in_str($url_string, '='))
			{
				$name_value = explode('=', $url_string);
			}
			debug::variable($name_value, 'name_value');
			if (is_array($name_value) && !empty($name_value))
			{
				if (isset($name_value[0]) and isset($name_value[1]))
				{
					$return_array[$name_value[0]] = urldecode($name_value[1]);
				}
			}
		}

		return $return_array;
	}

	public static function add_query_parameter_to_url($url, $query_parameter)
	{
		debug::on(false);
		debug::variable($url);
		debug::variable($query_parameter);

		if (Str::in_str($url, '?'))
		{
			$url .= '&'. $query_parameter;
		}
		else
		{
			$url .= '?'. $query_parameter;
		}

		return $url;
	}

}


?>