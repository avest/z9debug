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