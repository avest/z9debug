<?php
//===================================================================
// z9Debug
//===================================================================
// Authenticate.php
// --------------------
//
//       Date Created: 2018-10-22
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

namespace Z9\Debug\Console;

use debug;
use Facade\Dependency;
use Facade\Date;

class Authenticate
{
	public function __construct()
	{
	}

	public function is_valid_auth_token()
	{
		debug::on(false);
		debug::string('is_valid_auth_token()');
		$cms_cookie_name = 'z9debug_token';
		$cms_cookie_value = '';
		$cms_user = '';
		$cms_cookie_issued = '';
		$cms_cookie_expired = '';
		$cms_cookie_hash = '';
		$cms_calc_hash = '';
		$cms_auth_secret_key = debug::get('secret');
		$cms_public_part = '';

		if (isset($_COOKIE[$cms_cookie_name]))
		{
			$cms_cookie_value = $_COOKIE[$cms_cookie_name];
			list($cms_user, $cms_cookie_issued, $cms_cookie_expired, $cms_cookie_hash) = explode(":", $cms_cookie_value, 4);
			$cms_public_part = $cms_user.":".$cms_cookie_issued.":".$cms_cookie_expired;
			$cms_calc_hash = md5($cms_auth_secret_key.":".md5($cms_public_part.":".$cms_auth_secret_key));
		}
		debug::variable($cms_cookie_name);
		debug::variable($cms_cookie_value);
		debug::variable($cms_auth_secret_key);
		debug::variable($cms_user);
		debug::variable($cms_cookie_issued);
		debug::variable($cms_cookie_expired);
		debug::variable($cms_cookie_hash);
		debug::variable($cms_public_part);
		debug::variable($cms_calc_hash);

		$is_valid_auth_token = false;
		if ($cms_calc_hash == $cms_cookie_hash and strlen($cms_cookie_hash) > 0)
		{
			$is_valid_auth_token = true;
			debug::variable($is_valid_auth_token);
		}
		else
		{
			debug::variable($is_valid_auth_token);
		}


		return $is_valid_auth_token;
	}

	function set_auth_token($username='')
	{
		debug::on(false);
		$cms_cookie_name = 'z9debug_token';
		$cms_user = $username;
		$cms_auth_secret_key = debug::get('secret');

		$cms_cookie_issued = Date::convert_unix_date(time(), $dateformat="yyyy-mm-dd-hh-mm-ss");
		$cms_cookie_expired = Date::convert_unix_date(time()+31536000, $dateformat="yyyy-mm-dd-hh-mm-ss"); // 1 yr
		$cms_public_part = $cms_user.":".$cms_cookie_issued.":".$cms_cookie_expired;
		$cms_calc_hash = md5($cms_auth_secret_key.":".md5($cms_public_part.":".$cms_auth_secret_key));
		$cms_auth_token = $cms_public_part.":".$cms_calc_hash;

		if (debug::get('force_http'))
		{
			setcookie($cms_cookie_name, $cms_auth_token, time()+31536000,"/", null, false);
		}
		else
		{
			setcookie($cms_cookie_name, $cms_auth_token, time()+31536000,"/", null, true);
		}
		$_COOKIE[$cms_cookie_name] = $cms_auth_token;

		debug::variable($cms_cookie_name);
		debug::variable($cms_auth_secret_key);
		debug::variable($cms_user);
		debug::variable($cms_cookie_issued);
		debug::variable($cms_cookie_expired);
		debug::variable($cms_public_part);
		debug::variable($cms_calc_hash);

	}

}



?>