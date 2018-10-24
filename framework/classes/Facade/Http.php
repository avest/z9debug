<?php
//===================================================================
// Z9 Framework
//===================================================================
// Http.php
// --------------------
//       Date Created: 2005-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

// function http_parse_cookie2( $header )
// function http_parse_headers2( $header )
// function HTTPStatus($num)
// function multi_post_to_url($post_values_array, $post_url_array)
// function post_to_url($post_values, $post_url, $user_agent='', $referer='', $cookies='', $timeout=0)
// function post_to_url_to_file($post_values, $post_url, $post_file, $user_agent='', $referer='', $cookies='')
// function post_to_url2($post_values, $post_url, $user_agent='', $referer='', $cookies='', $timeout=0)
// function stream_post_to_url($post_array, $post_url, $user_agent='', $referer='', $cookies='')
// function unchunkHttpResponse($str=null)


namespace Facade;

use debug;
use Facade\Arrays;
use Facade\Str;

class Http
{
	public function _construct()
	{
	}

	// return:
	// cookies[0][key] = (string) 'session_id'
	// cookies[0][value] = (string) '12345'
	// cookies[0][path] = (string) '/'
	// cookies[1][key] = (string) 'a[1]'
	// cookies[1][value] = (string) '1'
	// cookies[1][path] = (string) '/'
	// cookies[2][key] = (string) 'a[2]'
	// cookies[2][value] = (string) '2'
	// cookies[2][path] = (string) '/'
	public static function http_parse_cookie2( $header )
	{
		debug::on(false);
		debug::string('http_parse_cookie2()');
		debug::variable($header, 'header');

		$cookies = array();
		if (is_array($header))
		{
			foreach ($header as $line)
			{
				debug::variable($line, 'line');

				if (preg_match( '/^Set-Cookie: /i', $line ))
				{
					$line = preg_replace( '/^Set-Cookie: /i', '', trim( $line ) );
					debug::variable($line, 'line');

					$csplit = explode( ';', $line );
					debug::variable($csplit, 'csplit');

					$cdata = array();
					debug::variable($cdata, 'cdata');

					foreach( $csplit as $data )
					{
						debug::variable($data, 'data');

						$cinfo = explode( '=', $data );
						debug::variable($cinfo, 'cinfo');

						if (isset($cinfo[0]))
						{
							$cinfo[0] = trim( $cinfo[0] );
							if ( $cinfo[0] == 'expires' )
							{
								$cinfo[1] = strtotime( $cinfo[1] );
							}
							if ( $cinfo[0] == 'secure' )
							{
								$cinfo[1] = "true";
							}
							if ( in_array( $cinfo[0], array( 'domain', 'expires', 'path', 'secure', 'comment' ) ) )
							{
								if (isset($cinfo[1]))
								{
									$cdata[trim( $cinfo[0] )] = $cinfo[1];
								}
							}
							else
							{
								if (isset($cinfo[0]))
								{
									$cdata['key'] = $cinfo[0];
								}
								if (isset($cinfo[1]))
								{
									$cdata['value'] = $cinfo[1];
								}
							}
						}
					}
					$cookies[] = $cdata;
				}
			}
		}
		return $cookies;
	}

	// return:
	// headers[status] = (string) 'HTTP/1.1 200 OK '
	// headers[Date] = (string) 'Tue, 30 Jun 2009 20:39:19 GMT'
	// headers[Server] = (string) 'Apache/2.2.11 (Unix) mod_ssl/2.2.11 OpenSSL/0.9.8b PHP/5.2.9'
	// headers[X-Powered-By] = (string) 'PHP/5.2.9'
	// headers[Set-Cookie][0] = (string) 'session_id=12345; path=/'
	// headers[Set-Cookie][1] = (string) 'a[1]=1; path=/'
	// headers[Set-Cookie][2] = (string) 'a[2]=2; path=/'
	// headers[Content-Length] = (string) '206'
	// headers[Content-Type] = (string) 'text/html'
	public static function http_parse_headers2( $header )
	{
		debug::on(false);
		debug::variable($header);

		$retVal = array();
		$fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
		debug::variable($fields, 'fields');

		if (Str::starts_with(strtoupper($fields[0]), 'HTTP'))
		{
			$retVal['status'] = $fields[0];
		}
		debug::variable($retVal);

		foreach( $fields as $field )
		{
			debug::variable($field);
			if( preg_match('/([^:]+): (.+)/m', $field, $match) )
			{
				debug::variable($match, 'match');
				//$match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
				$match[1] = preg_replace_callback(
					'/(?<=^|[\x09\x20\x2D])./',
					function($matches) {
						return strtoupper($matches[0]);
					},
					strtolower(trim($match[1]))
				);
				debug::variable($match[1]);

				$header_key = $match[1];
				debug::variable($header_key, 'header_key');

				$header_value = trim($match[2]);
				debug::variable($header_value, 'header_value');

				if( isset($retVal[$header_key]) )
				{
					if (is_array($retVal[$header_key]))
					{
						$retVal[$header_key][] = $header_value;
					}
					else
					{
						// convert header to an array
						$prev_value = $retVal[$header_key];
						debug::variable($prev_value, 'prev_value');
						unset($retVal[$header_key]);
						$retVal[$header_key] = array();
						$retVal[$header_key][] = $prev_value;
						$retVal[$header_key][] = $header_value;
					}
				}
				else
				{
					$retVal[$header_key] = $header_value;
				}
			}
		}
		return $retVal;
	}

	// alias of HTTPStatus
	public static function status($num)
	{
		self::HTTPStatus($num);
	}

	/**
	 * HTTP Protocol defined status codes
	 * @param int $num
	 */
	public static function HTTPStatus($num) {

	   static $http = array (
		  100 => "HTTP/1.1 100 Continue",
		  101 => "HTTP/1.1 101 Switching Protocols",
		  200 => "HTTP/1.1 200 OK",
		  201 => "HTTP/1.1 201 Created",
		  202 => "HTTP/1.1 202 Accepted",
		  203 => "HTTP/1.1 203 Non-Authoritative Information",
		  204 => "HTTP/1.1 204 No Content",
		  205 => "HTTP/1.1 205 Reset Content",
		  206 => "HTTP/1.1 206 Partial Content",
		  300 => "HTTP/1.1 300 Multiple Choices",
		  301 => "HTTP/1.1 301 Moved Permanently",
		  302 => "HTTP/1.1 302 Found",
		  303 => "HTTP/1.1 303 See Other",
		  304 => "HTTP/1.1 304 Not Modified",
		  305 => "HTTP/1.1 305 Use Proxy",
		  307 => "HTTP/1.1 307 Temporary Redirect",
		  400 => "HTTP/1.1 400 Bad Request",
		  401 => "HTTP/1.1 401 Unauthorized",
		  402 => "HTTP/1.1 402 Payment Required",
		  403 => "HTTP/1.1 403 Forbidden",
		  404 => "HTTP/1.1 404 Not Found",
		  405 => "HTTP/1.1 405 Method Not Allowed",
		  406 => "HTTP/1.1 406 Not Acceptable",
		  407 => "HTTP/1.1 407 Proxy Authentication Required",
		  408 => "HTTP/1.1 408 Request Time-out",
		  409 => "HTTP/1.1 409 Conflict",
		  410 => "HTTP/1.1 410 Gone",
		  411 => "HTTP/1.1 411 Length Required",
		  412 => "HTTP/1.1 412 Precondition Failed",
		  413 => "HTTP/1.1 413 Request Entity Too Large",
		  414 => "HTTP/1.1 414 Request-URI Too Large",
		  415 => "HTTP/1.1 415 Unsupported Media Type",
		  416 => "HTTP/1.1 416 Requested range not satisfiable",
		  417 => "HTTP/1.1 417 Expectation Failed",
		  500 => "HTTP/1.1 500 Internal Server Error",
		  501 => "HTTP/1.1 501 Not Implemented",
		  502 => "HTTP/1.1 502 Bad Gateway",
		  503 => "HTTP/1.1 503 Service Unavailable",
		  504 => "HTTP/1.1 504 Gateway Time-out"
	   );

	   header($http[$num]);
	}

	public static function multi_post_to_url($post_values_array, $post_url_array)
	{
		$libcurl = false;
		if (function_exists('curl_init'))
		{
			$libcurl = true;
		}
		debug::variable($libcurl, 'libcurl');
		debug::variable($post_values_array, 'post_values_array');
		debug::variable($post_url_array, 'post_url_array');
		//--------------------------------------------------------
		// urlencode input fields, build data string to be posted
		//--------------------------------------------------------

	    // post data
		if ($libcurl)
		{

			//create the multiple cURL handle
			$mh = curl_multi_init();
			debug::variable($mh, 'mh');
			debug::string("after curl_multi_init");


			if (is_array($post_url_array))
			{
				foreach ($post_url_array as $key => $post_url)
				{
					debug::variable($post_url, 'post_url');

					$data[$key] = '';
					foreach($post_values_array[$key] as $field_name => $field_value)
					{
						$data[$key] .= $field_name."=".urlencode($field_value)."&";
					}
					//debug::variable($data, 'data');
					// strip off last amperstand
					$data[$key] = Str::left($data[$key], strlen($data[$key])-1);

					//Replaces spaces with + for url formating. Would of used urlencode but it does
					//really weird things to the data.
					$data[$key]=str_replace(" ", "+", $data[$key]);

					debug::variable($data, 'data');

					// create cURL resources
					$ch[$key] = curl_init();
					debug::variable($ch, 'ch');

					// set URL and other appropriate options

					//curl_setopt($ch[$key], CURLOPT_SSLVERSION, 3);
					curl_setopt($ch[$key], CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch[$key], CURLOPT_SSL_VERIFYHOST, 2);

					curl_setopt($ch[$key], CURLOPT_URL, $post_url);
					curl_setopt($ch[$key], CURLOPT_HEADER, 0);
					curl_setopt($ch[$key], CURLOPT_POST, 1);
					curl_setopt($ch[$key], CURLOPT_POSTFIELDS, $data[$key]);
					curl_setopt($ch[$key], CURLOPT_RETURNTRANSFER, 1);
					debug::string("after curl_setopt");

					curl_multi_add_handle($mh, $ch[$key]);
					debug::string("after curl_multi_add_handle");

				}

				//execute the handles
				$running=null;
				do {
					$mrc = curl_multi_exec($mh, $running);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
				debug::string("after curl_multi_exec");

				while ($running && $mrc == CURLM_OK)
				{
				// wait for network
					if (curl_multi_select($mh) != -1)
					{
						// pull in any new data, or at least handle timeouts
						do
						{
							$mrc = curl_multi_exec($mh, $running);
						}
						while ($mrc == CURLM_CALL_MULTI_PERFORM);
					}
				}

				if ($mrc != CURLM_OK)
				{
					echo "curl_multi_exec() failed<br>";
				}

				$result_array = array();
				if (is_array($post_url_array))
				{
					foreach ($post_url_array as $key => $post_url)
					{
						$result_array[$key]['results'] = curl_multi_getcontent($ch[$key]);
						$result_array[$key]['results'] = explode("\n", $result_array[$key]['results']);

						$result_array[$key]['connection'] = curl_getinfo($ch[$key]);
					}
				}


				//close the handles
				if (is_array($post_url_array))
				{
					foreach ($post_url_array as $key => $post_url)
					{
						curl_multi_remove_handle($mh, $ch[$key]);
						curl_multi_remove_handle($mh, $ch[$key]);
					}
				}
				debug::string("after curl_multi_remove_handlec");

				curl_multi_close($mh);
				debug::string("after curl_multi_close");


			}
		}

		debug::variable($result_array, 'result_array');

		return $result_array;

	}

	public static function post_to_url($post_values, $post_url, $user_agent='', $referer='', $cookies='', $timeout=0)
	{
		debug::on(false);
		debug::variable($post_values, 'post_values');
		debug::variable($post_url, 'post_url');
		debug::variable($user_agent, 'user_agent');
		debug::variable($referer, 'referer');
		debug::variable($cookies, 'cookies');
		debug::variable($timeout, 'timeout');

		$libcurl = false;
		if (function_exists('curl_init'))
		{
			$libcurl = true;
		}
		debug::variable($libcurl, 'libcurl');

		//--------------------------------------------------------
		// urlencode input fields, build data string to be posted
		//--------------------------------------------------------
		$data = '';
		if (is_array($post_values))
		{
			foreach($post_values as $key => $value)
			{
				if (is_array($value))
				{
					foreach ($value as $key2 => $value2)
					{
						$data .= $key.'['.$key2."]=".urlencode($value2)."&";
					}
				}
				else
				{
					$data .= $key."=".urlencode($value)."&";
				}
			}
		}
		// strip off last amperstand
		$data = Str::left($data, strlen($data)-1);

		//Replaces spaces with + for url formating. Would of used urlencode but it does
		//really weird things to the data.
		$data=str_replace(" ", "+", $data);

		debug::variable($data, 'data');

		$cookie_data = '';

	    // post data
		if ($libcurl)
		{
			// Use curl functions built into PHP
			//echo curl_version()."<br>";
			$ch = curl_init();

			//curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

			curl_setopt ($ch, CURLOPT_URL, $post_url);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			if (strlen($timeout) > 0 && $timeout <> 0)
			{
				curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
			}
			$result_array = curl_exec ($ch);
			debug::variable($result_array);
			curl_close ($ch);
			$result_array = explode("\n", $result_array);
			debug::variable($result_array);
		}
		else
		{
			if (file_exists('/usr/bin/curl'))
			{
				$curl = '/usr/bin/curl --location --max-redirs 1';
			}
			else if (file_exists('/usr/local/bin/curl'))
			{
				$curl = "/usr/local/bin/curl --location --max-redirs 1";
			}

			if (strlen($user_agent) > 0)
			{
				$curl .= ' --user-agent '.$user_agent;
			}
			if (strlen($referer) > 0)
			{
				$curl .= ' --referer '.$referer;
			}
			if (strlen($cookie_data) > 0)
			{
				$curl .= ' --cookie '.$cookie_data;
			}
			if (strlen($timeout) > 0 && $timeout <> 0)
			{
				$curl .= ' --max-time '.$timeout;
			}
			debug::variable($curl, 'curl');

			exec("$curl -d \"$data\" $post_url", $result_array);
		}

		debug::variable($result_array, 'result_array');

		return $result_array;
	}

	// use this function when you want to save memory!!!
	public static function post_to_url_to_file($post_values, $post_url, $post_file, $user_agent='', $referer='', $cookies='')
	{
		$libcurl = false;
		if (function_exists('curl_init'))
		{
			$libcurl = true;
		}

		debug::variable($post_values, 'post_values');
		debug::variable($post_url, 'post_url');

		if (is_file($post_file))
		{
			@unlink($post_file);
		}

		//--------------------------------------------------------
		// urlencode input fields, build data string to be posted
		//--------------------------------------------------------
		$data = '';
		if (is_array($post_values))
		{
			foreach($post_values as $key => $value)
			{
				if (is_array($value))
				{
					foreach ($value as $key2 => $value2)
					{
						$data .= $key.'['.$key2."]=".urlencode($value2)."&";
					}
				}
				else
				{
					$data .= $key."=".urlencode($value)."&";
				}
			}
		}
		// strip off last amperstand
		$data = Str::left($data, strlen($data)-1);

		//Replaces spaces with + for url formating. Would of used urlencode but it does
		//really weird things to the data.
		$data=str_replace(" ", "+", $data);

		debug::variable($data, 'data');

		$cookie_data = '';

	    // post data
		if ($libcurl)
		{
			// Use curl functions built into PHP
			//echo curl_version()."<br>";

			$post_file_handle = fopen($post_file, "w");

			$ch = curl_init();

			//curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

			curl_setopt ($ch, CURLOPT_URL, $post_url);
			curl_setopt ($ch, CURLOPT_HEADER, 0);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
			//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_FILE, $post_file_handle);
			//curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 0);
			$result_array = curl_exec ($ch);
			curl_close ($ch);

			fclose($post_file_handle);
		}
		else
		{
			if (file_exists('/usr/bin/curl'))
			{
				$curl = '/usr/bin/curl --location --max-redirs 1';
			}
			else if (file_exists('/usr/local/bin/curl'))
			{
				$curl = "/usr/local/bin/curl --location --max-redirs 1";
			}

			if (strlen($user_agent) > 0)
			{
				$curl .= ' --user-agent '.$user_agent;
			}
			if (strlen($referer) > 0)
			{
				$curl .= ' --referer '.$referer;
			}
			if (strlen($cookie_data) > 0)
			{
				$curl .= ' --cookie '.$cookie_data;
			}
			$curl .= ' --output '.$post_file;
			debug::variable($curl, 'curl');

			$exec_string = "$curl -d \"$data\" $post_url";
			debug::variable($exec_string, 'exec_string');

			exec($exec_string, $result_array);
		}

		debug::variable($result_array, 'result_array');

		return $result_array;
	}

	// unlike post_to_url(), which only returns content, this function returns
	// content and headers.
	// $cookies is a single value or array that will be turned into a cookie header value
	public static function post_to_url2($post_values, $post_url, $user_agent='', $referer='', $cookies='', $timeout=0)
	{
		debug::variable($post_values, 'post_values');
		debug::variable($post_url, 'post_url');
		debug::variable($user_agent, 'user_agent');
		debug::variable($referer, 'referer');
		debug::variable($cookies, 'cookies');
		debug::variable($timeout, 'timeout');

		//$user_agent = str_replace(" ", "+", $user_agent);
		//$referer = str_replace(" ", "+", $referer);

		$libcurl = false;
		if (function_exists('curl_init'))
		{
			$libcurl = true;
		}
		debug::variable($libcurl, 'libcurl');

		//--------------------------------------------------------
		// urlencode input fields, build data string to be posted
		//--------------------------------------------------------
		$data = '';
		if (is_array($post_values))
		{
			foreach($post_values as $key => $value)
			{
				if (is_array($value))
				{
					foreach ($value as $key2 => $value2)
					{
						$data .= $key.'['.$key2."]=".urlencode($value2)."&";
					}
				}
				else
				{
					$data .= $key."=".urlencode($value)."&";
				}
			}
		}
		// strip off last amperstand
		$data = Str::left($data, strlen($data)-1);

		//Replaces spaces with + for url formating. Would of used urlencode but it does
		//really weird things to the data.
		$data=str_replace(" ", "+", $data);

		debug::variable($data, 'data');





		$cookie_data = '';
		if (is_array($cookies))
		{
			foreach($cookies as $key => $value)
			{
				if (is_array($value))
				{
					$cookie_keys = Arrays::get_array_keys($value);
					debug::variable($cookie_keys, 'cookie_keys');

					if (is_array($cookie_keys))
					{
						foreach ($cookie_keys as $key2 => $cookie_key)
						{
							debug::variable($cookie_key, 'cookie_key');
							$cookie_value = '';
							$eval_str = '$cookie_value = $cookies['.$key.']'.$cookie_key.';';
							debug::variable($eval_str, 'eval_str');
							eval($eval_str);
							debug::variable($cookie_value, 'cookie_value');
							$cookie_data .= $key.$cookie_key."=".urlencode($cookie_value)."; ";
						}
					}

				}
				else
				{
					$cookie_data .= $key."=".urlencode($value).";";
				}
			}
		}
		// strip off last semicolon and space
		$cookie_data = Str::left($cookie_data, strlen($cookie_data)-1);

		//Replaces spaces with + for url formating. Would of used urlencode but it does
		//really weird things to the data.
		$cookie_data = str_replace(" ", "+", $cookie_data);

		// this works for setting arrays
		//$cookie_data='test[1]=1; test[2]=2; test[a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a+a;';

		debug::variable($cookie_data, 'cookie_data');



	    // post data
		if ($libcurl)
		{
			// Use curl functions built into PHP
			//echo curl_version()."<br>";
			$ch = curl_init();

			//curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

			curl_setopt ($ch, CURLOPT_URL, $post_url);
			curl_setopt ($ch, CURLOPT_HEADER, 1);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt ($ch, CURLOPT_MAXREDIRS, 1);
			curl_setopt ($ch, CURLOPT_REFERER, $referer);
			curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt ($ch, CURLOPT_COOKIE, $cookie_data);

			if (strlen($timeout) > 0 && $timeout <> 0)
			{
				curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
			}
			$result_array = curl_exec ($ch);
			debug::variable($result_array, 'result_array');
			curl_close ($ch);
			$result_array = explode("\n", $result_array);
			debug::variable($result_array, 'result_array');
		}
		else
		{
			// -I --head = header only
			// -i --include = include header with body

			if (file_exists('/usr/bin/curl'))
			{
				$curl = '/usr/bin/curl --include --location --max-redirs 1';
			}
			else if (file_exists('/usr/local/bin/curl'))
			{
				$curl = "/usr/local/bin/curl --include --location --max-redirs 1";
			}

			if (strlen($user_agent) > 0)
			{
				$curl .= ' --user-agent '.$user_agent;
			}
			if (strlen($referer) > 0)
			{
				$curl .= ' --referer '.$referer;
			}
			if (strlen($cookie_data) > 0)
			{
				$curl .= ' --cookie '.$cookie_data;
			}
			if (strlen($timeout) > 0 && $timeout <> 0)
			{
				$curl .= ' --max-time '.$timeout;
			}
			debug::variable($curl, 'curl');

			exec("$curl -d \"$data\" $post_url", $result_array);
		}

		debug::variable($result_array, 'result_array');

		$header_lines = array();
		$content_lines = array();
		$headers_array = array();
		$cookies_array = array();

		// get headers & content
		$is_header = true;
		$is_continue = false;
		if (is_array($result_array))
		{
			//--------------------------------------------
			// skip HTTP/x.x 100 continue status section
			//--------------------------------------------
			$first_header = $result_array[0];
			debug::variable($first_header, 'first_header');

			if (Str::in_str($first_header, 'HTTP') && Str::in_str($first_header, '100'))
			{
				$is_continue = true;
			}

			foreach($result_array as $key => $line)
			{
				$trimmed_line = trim($line);
				if ($is_header)
				{
					if (!empty($trimmed_line))
					{
						if (!$is_continue)
						{
							$header_lines[] = $line;
						}
					}
					else
					{
						if ($is_continue)
						{
							$is_continue = false;
						}
						else
						{
							$is_header = false;
						}
					}
				}
				else
				{
					$content_lines[] = $line;
				}
				unset($result_array[$key]);
			}
		}

		// get headers_array
		$headers_array = implode("\r\n", $header_lines);
		$headers_array = self::http_parse_headers2($headers_array);

		// get cookies_array
		//$cookie_headers = $returned_headers['Set-Cookie'];
		$cookies_array = self::http_parse_cookie2($header_lines);
		debug::variable($cookies_array, 'cookies_array');

		$cookie_values = array();
		if (is_array($cookies_array))
		{
			foreach($cookies_array as $cookie_key => $cookie_value)
			{
				if (Str::in_str($cookie_value['key'], '['))
				{
					$c_keys = explode('[', str_replace(']','', $cookie_value['key']));
					debug::variable($c_keys, 'c_keys');

					$key_string = '';
					if (is_array($c_keys))
					{
						foreach($c_keys as $c_keys_key => $c_keys_value)
						{
							$key_string .= '['.$c_keys_value.']';
						}
					}
					debug::variable($key_string, 'key_string');

					$eval_str = '$cookie_values'.$key_string.' = $cookie_value[\'value\'];';
					debug::variable($eval_str, 'eval_str');

					eval($eval_str);
				}
				else
				{
					$cookie_values[$cookie_value['key']] = $cookie_value['value'];
				}
			}
		}
		debug::variable($cookie_values, 'cookie_values');

		$result_array = array(
			'headers' => $headers_array,
			'cookies' => $cookie_values,
			'content' => $content_lines,
		);

		return $result_array;
	}

	// unlike post_to_url(), which only returns content, this function returns
	// content and headers.
	// unlike post_to_url2(), we can also send $header_values.
	// $cookies is a single value or array that will be turned into a cookie header value
	public static function post_to_url3($post_values, $header_values, $post_url, $user_agent='', $referer='', $cookies='', $timeout=0)
	{
		debug::on(false);
		debug::variable($post_values);
		debug::variable($header_values);
		debug::variable($post_url);
		debug::variable($user_agent);
		debug::variable($referer);
		debug::variable($cookies);
		debug::variable($timeout);

		//$user_agent = str_replace(" ", "+", $user_agent);
		//$referer = str_replace(" ", "+", $referer);

		$libcurl = false;
		if (function_exists('curl_init'))
		{
			$libcurl = true;
		}
		debug::variable($libcurl);

		//--------------------------------------------------------
		// urlencode input fields, build data string to be posted
		//--------------------------------------------------------
		$data = '';
		if (is_array($post_values))
		{
			foreach($post_values as $key => $value)
			{
				if (is_array($value))
				{
					foreach ($value as $key2 => $value2)
					{
						$data .= $key.'['.$key2."]=".urlencode($value2)."&";
					}
				}
				else
				{
					$data .= $key."=".urlencode($value)."&";
				}
			}
		}
		// strip off last amperstand
		$data = Str::left($data, strlen($data)-1);

		//Replaces spaces with + for url formating. Would of used urlencode but it does
		//really weird things to the data.
		$data=str_replace(" ", "+", $data);

		debug::variable($data, 'data');



		$cookie_data = '';
		if (is_array($cookies))
		{
			foreach($cookies as $key => $value)
			{
				if (is_array($value))
				{
					$cookie_keys = Arrays::get_array_keys($value);
					debug::variable($cookie_keys, 'cookie_keys');

					if (is_array($cookie_keys))
					{
						foreach ($cookie_keys as $key2 => $cookie_key)
						{
							debug::variable($cookie_key, 'cookie_key');
							$cookie_value = '';
							$eval_str = '$cookie_value = $cookies['.$key.']'.$cookie_key.';';
							debug::variable($eval_str, 'eval_str');
							eval($eval_str);
							debug::variable($cookie_value, 'cookie_value');
							$cookie_data .= $key.$cookie_key."=".urlencode($cookie_value)."; ";
						}
					}

				}
				else
				{
					$cookie_data .= $key."=".urlencode($value).";";
				}
			}
		}
		// strip off last semicolon and space
		$cookie_data = Str::left($cookie_data, strlen($cookie_data)-1);

		//Replaces spaces with + for url formating. Would of used urlencode but it does
		//really weird things to the data.
		$cookie_data = str_replace(" ", "+", $cookie_data);

		// this works for setting arrays
		//$cookie_data='test[1]=1; test[2]=2; test[a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a+a;';

		debug::variable($cookie_data, 'cookie_data');

		$header_data = array();
		if (is_array($header_values))
		{
			foreach ($header_values as $header_key => $header_value)
			{
				$header_data[] = $header_key.': '.$header_value;
			}
		}
		debug::variable($header_data);

	    // post data
		if ($libcurl)
		{
			// Use curl functions built into PHP
			//echo curl_version()."<br>";
			$ch = curl_init();

			//curl_setopt($ch, CURLOPT_SSLVERSION, 3);

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

			curl_setopt ($ch, CURLOPT_URL, $post_url);
			curl_setopt ($ch, CURLOPT_HEADER, 1);
			if (!empty($data))
			{
				curl_setopt ($ch, CURLOPT_POST, 1);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
			}
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			//curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt ($ch, CURLOPT_MAXREDIRS, 1);
			if (!empty($referer))
			{
				curl_setopt ($ch, CURLOPT_REFERER, $referer);
			}
			if (!empty($user_agent))
			{
				curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
			}
			if (!empty($cookie_data))
			{
				curl_setopt ($ch, CURLOPT_COOKIE, $cookie_data);
			}
			if (!empty($header_values))
			{
				curl_setopt ($ch, CURLOPT_HTTPHEADER, $header_data);
			}
			if (strlen($timeout) > 0 && $timeout <> 0)
			{
				curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
			}
			$result_array = curl_exec ($ch);
			debug::variable($result_array, 'result_array');
			curl_close ($ch);
			$result_array = explode("\n", $result_array);
			debug::variable($result_array, 'result_array');
		}
		else
		{
			// -I --head = header only
			// -i --include = include header with body

			if (file_exists('/usr/bin/curl'))
			{
				$curl = '/usr/bin/curl --include --location --max-redirs 1';
			}
			else if (file_exists('/usr/local/bin/curl'))
			{
				$curl = "/usr/local/bin/curl --include --location --max-redirs 1";
			}

			if (strlen($user_agent) > 0)
			{
				$curl .= ' --user-agent '.$user_agent;
			}
			if (strlen($referer) > 0)
			{
				$curl .= ' --referer '.$referer;
			}
			if (strlen($cookie_data) > 0)
			{
				$curl .= ' --cookie '.$cookie_data;
			}
			if (strlen($timeout) > 0 && $timeout <> 0)
			{
				$curl .= ' --max-time '.$timeout;
			}
			if (is_array($header_values))
			{
				foreach ($header_values as $header_key => $header_value)
				{
					$curl .= ' -H "'.$header_key.': '.$header_value.'"';
				}
			}

			if (!empty($data))
			{
				$curl .= " -d \"$data\"";
			}

			$curl .= " ".$post_url;
			debug::variable($curl, 'curl');

			exec($curl, $result_array);

		}

		debug::variable($result_array, 'result_array');

		$header_lines = array();
		$content_lines = array();
		$headers_array = array();
		$cookies_array = array();

		// get headers & content
		$is_header = true;
		$is_continue = false;
		if (is_array($result_array))
		{
			//--------------------------------------------
			// skip HTTP/x.x 100 continue status section
			//--------------------------------------------
			$first_header = $result_array[0];
			debug::variable($first_header, 'first_header');

			if (Str::in_str($first_header, 'HTTP') && Str::in_str($first_header, '100'))
			{
				$is_continue = true;
			}

			foreach($result_array as $key => $line)
			{
				$trimmed_line = trim($line);
				if ($is_header)
				{
					if (!empty($trimmed_line))
					{
						if (!$is_continue)
						{
							$header_lines[] = $line;
						}
					}
					else
					{
						if ($is_continue)
						{
							$is_continue = false;
						}
						else
						{
							$is_header = false;
						}
					}
				}
				else
				{
					$content_lines[] = $line;
				}
				unset($result_array[$key]);
			}
		}

		// get headers_array
		$headers_array = implode("\r\n", $header_lines);
		$headers_array = self::http_parse_headers2($headers_array);

		// get cookies_array
		//$cookie_headers = $returned_headers['Set-Cookie'];
		$cookies_array = self::http_parse_cookie2($header_lines);
		debug::variable($cookies_array, 'cookies_array');

		$cookie_values = array();
		if (is_array($cookies_array))
		{
			foreach($cookies_array as $cookie_key => $cookie_value)
			{
				if (Str::in_str($cookie_value['key'], '['))
				{
					$c_keys = explode('[', str_replace(']','', $cookie_value['key']));
					debug::variable($c_keys, 'c_keys');

					$key_string = '';
					if (is_array($c_keys))
					{
						foreach($c_keys as $c_keys_key => $c_keys_value)
						{
							$key_string .= '['.$c_keys_value.']';
						}
					}
					debug::variable($key_string, 'key_string');

					$eval_str = '$cookie_values'.$key_string.' = $cookie_value[\'value\'];';
					debug::variable($eval_str, 'eval_str');

					eval($eval_str);
				}
				else
				{
					$cookie_values[$cookie_value['key']] = $cookie_value['value'];
				}
			}
		}
		debug::variable($cookie_values, 'cookie_values');

		$result_array = array(
			'headers' => $headers_array,
			'cookies' => $cookie_values,
			'content' => $content_lines,
		);

		return $result_array;
	}

	// unlike post_to_url(), which posts key/value pairs, this function post raw data
	// $cookies is a single value or array that will be turned into a cookie header value
	public static function post_raw_data_to_url($raw_data, $post_url, $headers=array(), $user_agent='', $referer='', $cookies='', $timeout=0)
	{
		debug::on(false);
		debug::string('post_raw_data_to_url()');

		debug::variable($raw_data, 'raw_data');
		debug::variable($post_url, 'post_url');
		debug::variable($headers, 'headers');
		debug::variable($user_agent, 'user_agent');
		debug::variable($referer, 'referer');
		debug::variable($cookies, 'cookies');
		debug::variable($timeout, 'timeout');

		//$user_agent = str_replace(" ", "+", $user_agent);
		//$referer = str_replace(" ", "+", $referer);

		$libcurl = false;
		if (function_exists('curl_init'))
		{
			$libcurl = true;
		}
		debug::variable($libcurl, 'libcurl');

		//--------------------------------------------------------
		// urlencode input fields, build data string to be posted
		//--------------------------------------------------------
		$data = $raw_data;
		//$data = urlencode($data);
		debug::variable($data, 'data');

		$cookie_data = '';
		if (is_array($cookies))
		{
			foreach($cookies as $key => $value)
			{
				if (is_array($value))
				{
					$cookie_keys = Arrays::get_array_keys($value);
					debug::variable($cookie_keys, 'cookie_keys');

					if (is_array($cookie_keys))
					{
						foreach ($cookie_keys as $key2 => $cookie_key)
						{
							debug::variable($cookie_key, 'cookie_key');
							$eval_str = '$cookie_value = $cookies['.$key.']'.$cookie_key.';';
							debug::variable($eval_str, 'eval_str');
							eval($eval_str);
							debug::variable($cookie_value, 'cookie_value');
							$cookie_data .= $key.$cookie_key."=".urlencode($cookie_value)."; ";
						}
					}

				}
				else
				{
					$cookie_data .= $key."=".urlencode($value).";";
				}
			}
		}
		// strip off last semicolon and space
		$cookie_data = Str::left($cookie_data, strlen($cookie_data)-1);

		//Replaces spaces with + for url formating. Would of used urlencode but it does
		//really weird things to the data.
		$cookie_data = str_replace(" ", "+", $cookie_data);

		// this works for setting arrays
		//$cookie_data='test[1]=1; test[2]=2; test[a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a a;';
		//$cookie_data='test[1]=1; test[2]=2; test[a+a]=a+a;';

		debug::variable($cookie_data, 'cookie_data');


		// post data
		if ($libcurl)
		{
			// Use curl functions built into PHP
			//echo curl_version()."<br>";
			$ch = curl_init();

			//curl_setopt($ch, CURLOPT_SSLVERSION, 3);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // default
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

			curl_setopt ($ch, CURLOPT_URL, $post_url);
			curl_setopt ($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);

			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);

			curl_setopt ($ch, CURLOPT_HEADER, 1);

			curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt ($ch, CURLOPT_MAXREDIRS, 1);
			curl_setopt ($ch, CURLOPT_REFERER, $referer);
			curl_setopt ($ch, CURLOPT_USERAGENT, $user_agent);
			curl_setopt ($ch, CURLOPT_COOKIE, $cookie_data);

			if (strlen($timeout) > 0 && $timeout <> 0)
			{
				curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
			}

			if (is_array($headers) && !empty($headers))
			{
				curl_setopt ($ch, CURLOPT_HTTPHEADER, $headers);
			}

			$result_array = curl_exec ($ch);
			debug::variable($result_array, 'result_array');

			curl_close ($ch);
			$result_array = explode("\n", $result_array);
			debug::variable($result_array, 'result_array');
		}
		else
		{
			// -I --head = header only
			// -i --include = include header with body

			if (file_exists('/usr/bin/curl'))
			{
				$curl = '/usr/bin/curl --include --location --max-redirs 1';
			}
			else if (file_exists('/usr/local/bin/curl'))
			{
				$curl = "/usr/local/bin/curl --include --location --max-redirs 1";
			}

			if (strlen($user_agent) > 0)
			{
				$curl .= ' --user-agent '.$user_agent;
			}
			if (strlen($referer) > 0)
			{
				$curl .= ' --referer '.$referer;
			}
			if (strlen($cookie) > 0)
			{
				$curl .= ' --cookie '.$cookie_data;
			}
			if (strlen($timeout) > 0 && $timeout <> 0)
			{
				$curl .= ' --max-time '.$timeout;
			}
			debug::variable($curl, 'curl');

			exec("$curl -d \"$data\" $post_url", $result_array);
		}

		debug::variable($result_array, 'result_array');

		$header_lines = array();
		$content_lines = array();
		$headers_array = array();
		$cookies_array = array();

		// get headers & content
		$is_header = true;
		$is_continue = false;
		if (is_array($result_array))
		{
			//--------------------------------------------
			// skip HTTP/x.x 100 continue status section
			//--------------------------------------------
			$first_header = $result_array[0];
			debug::variable($first_header, 'first_header');

			if (Str::in_str($first_header, 'HTTP') && Str::in_str($first_header, '100'))
			{
				$is_continue = true;
			}

			foreach($result_array as $key => $line)
			{
				$trimmed_line = trim($line);
				if ($is_header)
				{
					if (!empty($trimmed_line))
					{
						if (!$is_continue)
						{
							$header_lines[] = $line;
						}
					}
					else
					{
						if ($is_continue)
						{
							$is_continue = false;
						}
						else
						{
							$is_header = false;
						}
					}
				}
				else
				{
					$content_lines[] = $line;
				}
				unset($result_array[$key]);
			}
		}

		// get headers_array
		$headers_array = implode("\r\n", $header_lines);
		$headers_array = self::http_parse_headers2($headers_array);

		// get cookies_array
		//$cookie_headers = $returned_headers['Set-Cookie'];
		$cookies_array = self::http_parse_cookie2($header_lines);
		debug::variable($cookies_array, 'cookies_array');

		$cookie_values = array();
		if (is_array($cookies_array))
		{
			foreach($cookies_array as $cookie_key => $cookie_value)
			{
				if (Str::in_str($cookie_value['key'], '['))
				{
					$c_keys = explode('[', str_replace(']','', $cookie_value['key']));
					debug::variable($c_keys, 'c_keys');

					$key_string = '';
					if (is_array($c_keys))
					{
						foreach($c_keys as $c_keys_key => $c_keys_value)
						{
							$key_string .= '['.$c_keys_value.']';
						}
					}
					debug::variable($key_string, 'key_string');

					$eval_str = '$cookie_values'.$key_string.' = $cookie_value[\'value\'];';
					debug::variable($eval_str, 'eval_str');

					eval($eval_str);
				}
				else
				{
					$cookie_values[$cookie_value['key']] = $cookie_value['value'];
				}
			}
		}
		debug::variable($cookie_values, 'cookie_values');

		$result_array = array(
			'headers' => $headers_array,
			'cookies' => $cookie_values,
			'content' => $content_lines,
		);
		debug::variable($result_array, 'result_array');

		return $result_array;
	}

	// this function streams the output of a post to a url while the page is generating
	public static function stream_post_to_url($post_array, $post_url, $user_agent='', $referer='', $cookies='')
	{
		debug::variable($post_array, 'post_array');
		debug::variable($post_url, 'post_url');

		// generate post_string
		$post_string = '';
		if(is_array($post_array) && count($post_array) > 0)
		{
			// Get a blank slate
			$tempString = array();

			// Convert post_array into a query string (ie animal=dog&sport=baseball)
			foreach ($post_array as $key => $value)
			{
				if(strlen(trim($value))>0)
				{
					$tempString[] = $key . "=" . urlencode($value);
				}
			}

			$post_string = join('&', $tempString);
		}
		debug::variable($post_string, 'post_string');

		$post_url_array = @parse_url($post_url);
		debug::variable($post_url_array, 'post_url_array');
		$host = $post_url_array['host'];
		debug::variable($host, 'host');
		$path = $post_url_array['path'];
		debug::variable($path, 'path');

		$fp = fsockopen($host, 80);
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: " . strlen($post_string) . "\r\n");
		if ($user_agent)
		{
			fputs($fp, "User-Agent: $user_agent\r\n");
		}
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $post_string);

		$raw_content = '';
		$http_content = '';
		$beyond_header = false;
		$header_content = '';
		$header_array = array();
		$headers = array();
		$chunked_content = false;
		$chunked_content_len = '';

		while (!feof($fp))
		{
			if (!$beyond_header)
			{
				// get one header line, up to 8192 characters in length
				$request = fgets($fp,8192);
			}
			else
			{
				if ($chunked_content)
				{
					$chunked_content_len_request = fgets($fp,8192);
					$chunked_content_len_request = trim($chunked_content_len_request);
					debug::variable($chunked_content_len_request, 'chunked_content_len_request');
					$chunked_content_len = hexdec($chunked_content_len_request)+2;
					debug::variable($chunked_content_len, 'chunked_content_len');
					$request = fread($fp,$chunked_content_len);
					debug::variable($request, 'request');
				}
				else
				{
					$request = fread($fp, 40);
				}
			}

			$raw_content .= $request;
			if (!$beyond_header)
			{
				//---------------------------
				// if end of header found
				//---------------------------
				if (stripos($raw_content, "\r\n\r\n") !== FALSE)
				{
					debug::string('end of header content found');

					$hc = explode("\r\n\r\n",  $raw_content);
					debug::variable($hc, 'hc');

					$header_array = explode("\r\n",  $hc[0]);
					debug::variable($header_array, 'header_array');

					$header_content = $hc[0];
					unset($hc[0]);
					debug::variable($header_content, 'header_content');

					$http_content = implode("\r\n", $hc);
					debug::variable($http_content, 'http_content');

					if (is_array($header_array))
					{
						foreach($header_array as $key => $header)
						{
							$a = "";
							$b = "";
							if(stripos($header, ":") !== FALSE)
							{
								list($a, $b) = explode(":", $header);
								$headers[trim($a)] = trim($b);
							}
						}
					}
					debug::variable($headers, 'headers');

					if ($headers['Transfer-Encoding'] == 'chunked')
					{
						$chunked_content = true;
					}
					debug::variable($chunked_content, 'chunked_content');

					$request = $http_content;
					$beyond_header = true;
				}
				else
				{
					$request = '';
				}
			}
			else
			{
				$http_content .= $request;
			}
			echo $request;
			ob_flush(); flush();

		}
		fclose($fp);

		debug::variable($raw_content, 'raw_content');
		debug::variable($header_content, 'header_content');
		debug::variable($headers, 'headers');
		debug::variable($chunked_content, 'chunked_content');
		debug::variable($http_content, 'http_content');
	}

	// this function assumes the headers have already been removed
	public static function unchunkHttpResponse($str=null)
	{
		if (!is_string($str) or strlen($str) < 1) { return false; }
		$eol = "\r\n";
		$add = strlen($eol);
		$tmp = $str;
		$str = '';
		do
		{
			$tmp = ltrim($tmp);
			debug::variable($tmp, 'tmp');
			$pos = strpos($tmp, $eol);
			debug::variable($pos, 'pos');
			if ($pos === false) { return false; }
			$len = hexdec(substr($tmp,0,$pos));
			debug::variable($len, 'len');
			if (!is_numeric($len) or $len < 0) { return false; }
			$str .= substr($tmp, ($pos + $add), $len);
			debug::variable($str, 'str');
			$tmp  = substr($tmp, ($len + $pos + $add));
			debug::variable($tmp, 'tmp');
			$check = trim($tmp);
			debug::variable($check, 'check');
		}
		while(!empty($check));
		unset($tmp);
		return $str;
	}

	// use this to prevent the browser from using cache
	public static function no_cache()
	{
		header('Expires: Sun, 01 Jan 2014 00:00:00 GMT');
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}

	public static function location($url)
	{
		header("Location: ".$url);
	}

}