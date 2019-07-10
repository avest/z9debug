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

}


?>