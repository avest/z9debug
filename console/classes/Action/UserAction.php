<?php
//===================================================================
// z9Debug
//===================================================================
// UserAction.php
// --------------------
//
//       Date Created: 2018-10-22
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

namespace Z9\Debug\Console\Action;

use debug;
use Facade\Dependency;
use Facade\File;
use Z9\Debug\Console\Authenticate;
use Facade\Http;
use Facade\Str;

class UserAction
{
	/** @var \Z9\Debug\Console\Authenticate */
	private $authenticate;

	function __construct()
	{
		$this->authenticate = Dependency::inject('\Z9\Debug\Console\Authenticate');
	}

	public function login($username, $password)
	{
		debug::on(false);
		debug::variable($username);
		debug::variable($password);

		// check that strong password is used...
		$strong_password_settings = debug::get('strong_password');
		debug::variable($strong_password_settings);

		$min_chars = (isset($strong_password_settings['min_chars'])) ? $strong_password_settings['min_chars'] : 8;
		debug::variable($min_chars);

		$req_upper_and_lower = (isset($strong_password_settings['req_upper_and_lower'])) ? $strong_password_settings['req_upper_and_lower'] : true;
		debug::variable($req_upper_and_lower);

		$req_char = (isset($strong_password_settings['req_char'])) ? $strong_password_settings['req_char'] : true;
		debug::variable($req_char);

		$req_digit = (isset($strong_password_settings['req_digit'])) ? $strong_password_settings['req_digit'] : false;
		debug::variable($req_digit);

		$req_symbol = (isset($strong_password_settings['req_symbol'])) ? $strong_password_settings['req_symbol'] : false;
		debug::variable($req_symbol);

		$req_digit_or_symbol = (isset($strong_password_settings['req_digit_or_symbol'])) ? $strong_password_settings['req_digit_or_symbol'] : true;
		debug::variable($req_digit_or_symbol);

		$is_strong_password = Str::is_strong_password($password, $min_chars, $req_upper_and_lower, $req_char, $req_digit, $req_symbol, $req_digit_or_symbol);
		debug::variable($is_strong_password);

		if (!$is_strong_password)
		{
			return false;
		}

		$remote_authentication = debug::get('remote_authentication');
		if (!empty($remote_authentication))
		{
			//--------------------------
			// remote authentication
			//--------------------------

			$post_url = debug::get('remote_authentication');
			debug::variable($post_url);
			//echo "post_url=<pre>".print_r($post_url, true)."</pre><br>";


			// check that post_url is https!
			$post_url_array = parse_url($post_url);
			debug::variable($post_url_array);

			if (!isset($post_url_array['scheme']) || strtolower($post_url_array['scheme']) <> 'https')
			{
				echo "Invalid remote_authentication setting. HTTPS required.<br>";
				exit();
			}

			$post_data = array(
				'username' => $username,
				'password' => $password,
			);
			debug::variable($post_data);
			//echo "post_data=<pre>".print_r($post_data, true)."</pre><br>";

			$result = Http::post_to_url($post_data, $post_url);
			debug::variable($result);

			$is_valid_authentication = implode('',$result);
			debug::variable($is_valid_authentication);
			//echo "is_valid_authentication=<pre>".print_r($is_valid_authentication, true)."</pre><br>";

			if ($is_valid_authentication == '1')
			{
				$this->authenticate->set_auth_token($username);

				return true;
			}

		}
		else
		{
			//----------------------------------
			// single password authentication
			//----------------------------------
			$password = $_POST['password'];
			debug::variable($password);

			if ($password == debug::get('password'))
			{
				$this->authenticate->set_auth_token();

				return true;
			}
		}

		return false;

	}

	public function logout()
	{
		// logout
		$token_cookie_name = 'z9debug_token';
		$login_cookie_name = 'z9debug_login';
		$_COOKIE[$token_cookie_name] = '';
		$_COOKIE[$login_cookie_name] = '';
		if (debug::get('force_http'))
		{
			setcookie($token_cookie_name, '', null,"/", null, false);
			setcookie($login_cookie_name, '', null,"/", null, false);
		}
		else
		{
			setcookie($token_cookie_name, '', null,"/", null, true);
			setcookie($login_cookie_name, '', null,"/", null, true);
		}
		debug::string('clearing cookie');
	}

}

?>