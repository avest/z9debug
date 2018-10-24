<?php
//===================================================================
// Z9 Framework
//===================================================================
// User.php
// --------------------
//       Date Created: 2005-01-01
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================


namespace Z9\Framework;

use debug;
use Mlaphp\Request;
use Facade\Config;
use Z9\Framework\Hooks;
use Facade\Gateway;
use Facade\Date;
use Facade\Dependency;

class User
{

	public $data = array();

	/** @var Request  */
	private $request = null;
	/** @var \Z9\Framework\Hooks  */
	private $hooks = null;

	public function __construct()
	{
		debug::on(false);
		debug::string('__construct');

		$this->request = Dependency::inject('Request');
		$this->hooks = Dependency::inject('\Z9\Framework\Hooks');
	}

	// fe_user_id
	// fe_user_active
	// fe_first_name
	// fe_last_name
	// fe_full_name
	// fe_last_login
	// fe_username
	// fe_is_logged_in

	// be_user_id
	// be_user_active
	// be_first_name
	// be_last_name
	// be_full_name
	// be_last_login
	// be_username
	// be_is_logged_in

	public function init_data()
	{
		debug::on(false);

		$this->data['fe_user_id'] = '';
		$this->data['fe_user_active'] = false;
		$this->data['fe_first_name'] = '';
		$this->data['fe_last_name'] = '';
		$this->data['fe_full_name'] = '';
		$this->data['fe_last_login'] = '';
		$this->data['fe_username'] = '';
		$this->data['fe_is_logged_in'] = false;

		$this->data['be_user_id'] = '';
		$this->data['be_user_active'] = false;
		$this->data['be_first_name'] = '';
		$this->data['be_last_name'] = '';
		$this->data['be_full_name'] = '';
		$this->data['be_last_login'] = '';
		$this->data['be_username'] = '';
		$this->data['be_is_logged_in'] = false;

//		//-----------------------------------------------------------------------------
//		// we have to determine if user is logged in, if only for the cms command bar
//		//-----------------------------------------------------------------------------
//		debug::variable(Config::get('cms.UserTokenName'), 'cms.UserTokenName');
//
//		$this->data['fe_user_id'] = $this->cms_get_logged_in_user_id(Config::get('cms.AuthSecretKey'));
//		debug::variable($this->data['fe_user_id'], 'this->data[fe_user_id]');
//
//		if (strlen($this->data['fe_user_id']) > 0)
//		{
//			$this->data['fe_is_logged_in'] = true;
//			debug::string("User Logged In.");
//
//			$user = Gateway::_('Z9\Cms\Users')->cms_get_user_info($this->data['fe_user_id']);
//			debug::variable($user, 'user');
//
//			$this->data['fe_user_active'] = false;
//			if ($user['Active'] == 1)
//			{
//				$this->data['fe_user_active'] = true;
//			}
//			$this->data['fe_first_name'] = $user['fname'];
//			$this->data['fe_last_name'] = $user['lname'];
//			$this->data['fe_full_name'] = $user['fname'].' '.$user['lname'];
//			$this->data['fe_last_login'] = $user['LastLogin'];
//			$this->data['fe_username'] = $user['login'];
//		}
//		else
//		{
//			debug::string("User Not Logged In.");
//		}


//		debug::constant(Config::get('cms.AdminTokenName'), 'cms.AdminTokenName');
//
//		$this->data['be_user_id'] = $this->cms_get_logged_in_admin_id(Config::get('cms.AuthSecretKey'));
//		debug::variable($this->data['be_user_id'], 'this->data[be_user_id]');
//
//		if (strlen($this->data['be_user_id']) > 0)
//		{
//			$this->data['be_is_logged_in'] = true;
//			debug::string("Admin Logged In.");
//
//			$user = Gateway::_('Z9\Cms\Users')->cms_get_user_info($this->data['be_user_id']);
//			debug::variable($user, 'user');
//
//			$this->data['be_user_active'] = false;
//			if ($user['Active'] == 1)
//			{
//				$this->data['be_user_active'] = true;
//			}
//			$this->data['be_first_name'] = $user['fname'];
//			$this->data['be_last_name'] = $user['lname'];
//			$this->data['be_full_name'] = $user['fname'].' '.$user['lname'];
//			$this->data['be_last_login'] = $user['LastLogin'];
//			$this->data['be_username'] = $user['login'];
//		}
//		else
//		{
//			debug::string("Admin Not Logged In.");
//		}

		$this->hooks->do_action('user_init_data');

		debug::variable($this->data, 'this->data');

		if (!Config::get('cms.is_installed'))
		{
			debug::set_cms_user($this->data);
		}

	}

//	//--------------------------------------------------------------------------------
//	//  BACKEND
//	// 	Name:  cms_get_logged_in_user_id
//	// 	Description:
//	//		This function returns a user id of a logged in user if the cookie auth token
//	//		is valid.
//	//  Input:  $cms_secret_key
//	//	Output: $cms_user_id
//	//
//	//  example cms_admin_token cookie
//	//  100:2004-09-16-20-56-30:2004-09-17-20-56-30:6edf52d5689bffb5a8f59c7f3a2a0178
//	//  [userid]:[cookie issued]:[cookie expires]:[hash]
//	//--------------------------------------------------------------------------------
//	function cms_get_logged_in_user_id($cms_auth_secret_key)
//	{
//		debug::on(false);
//
//		$cms_cookie_name = Config::get('cms.UserTokenName');
//		$cms_cookie_value = '';
//		$cms_user = '';
//		$cms_cookie_issued = '';
//		$cms_cookie_expired = '';
//		$cms_cookie_hash = '';
//		$cms_calc_hash = '';
//		$cms_public_part = '';
//		if (isset($_COOKIE[Config::get('cms.UserTokenName')]))
//		{
//			$cms_cookie_value = $_COOKIE[Config::get('cms.UserTokenName')];
//			if (!empty($cms_cookie_value))
//			{
//				list($cms_user, $cms_cookie_issued, $cms_cookie_expired, $cms_cookie_hash) = explode(":", $cms_cookie_value, 4);
//				$cms_public_part = $cms_user.":".$cms_cookie_issued.":".$cms_cookie_expired;
//				$cms_calc_hash = md5($cms_auth_secret_key.":".md5($cms_public_part.":".$cms_auth_secret_key));
//			}
//		}
//		debug::variable($cms_cookie_name, 'cms_cookie_name');
//		debug::variable($cms_cookie_value, 'cms_cookie_value');
//		debug::variable($cms_auth_secret_key, 'cms_auth_secret_key');
//		debug::variable($cms_user, 'cms_user');
//		debug::variable($cms_cookie_issued, 'cms_cookie_issued');
//		debug::variable($cms_cookie_expired, 'cms_cookie_expired');
//		debug::variable($cms_cookie_hash, 'cms_cookie_hash');
//		debug::variable($cms_public_part, 'cms_public_part');
//		debug::variable($cms_calc_hash, 'cms_calc_hash');
//		if ($cms_calc_hash == $cms_cookie_hash and strlen($cms_cookie_hash) > 0)
//		{
//			$cms_user_id = $cms_user;
//		}
//		else
//		{
//			$cms_user_id = "";
//		}
//		return $cms_user_id;
//	}

//	//--------------------------------------------------------------------------------
//	//  BACKEND
//	// 	Name:  cms_get_logged_in_admin_id
//	// 	Description:
//	//		This function returns a user id of a logged in admin if the cookie auth token
//	//		is valid.
//	//  Input:  $cms_secret_key
//	//	Output: $cms_user_id
//	//
//	//  example cms_admin_token cookie
//	//  100:2004-09-16-20-56-30:2004-09-17-20-56-30:6edf52d5689bffb5a8f59c7f3a2a0178
//	//  [userid]:[cookie issued]:[cookie expires]:[hash]
//	//--------------------------------------------------------------------------------
//	function cms_get_logged_in_admin_id($cms_auth_secret_key)
//	{
//		debug::on(false);
//
//		$cms_cookie_name = Config::get('cms.AdminTokenName');
//		$cms_cookie_value = '';
//		$cms_user = '';
//		$cms_cookie_issued = '';
//		$cms_cookie_expired = '';
//		$cms_cookie_hash = '';
//		$cms_calc_hash = '';
//		$cms_public_part = '';
//		if (isset($_COOKIE[Config::get('cms.AdminTokenName')]))
//		{
//			$cms_cookie_value = $_COOKIE[Config::get('cms.AdminTokenName')];
//			list($cms_user, $cms_cookie_issued, $cms_cookie_expired, $cms_cookie_hash) = explode(":", $cms_cookie_value, 4);
//			$cms_public_part = $cms_user.":".$cms_cookie_issued.":".$cms_cookie_expired;
//			$cms_calc_hash = md5($cms_auth_secret_key.":".md5($cms_public_part.":".$cms_auth_secret_key));
//		}
//		debug::variable($cms_cookie_name, 'cms_cookie_name');
//		debug::variable($cms_cookie_value, 'cms_cookie_value');
//		debug::variable($cms_auth_secret_key, 'cms_auth_secret_key');
//		debug::variable($cms_user, 'cms_user');
//		debug::variable($cms_cookie_issued, 'cms_cookie_issued');
//		debug::variable($cms_cookie_expired, 'cms_cookie_expired');
//		debug::variable($cms_cookie_hash, 'cms_cookie_hash');
//		debug::variable($cms_public_part, 'cms_public_part');
//		debug::variable($cms_calc_hash, 'cms_calc_hash');
//		if ($cms_calc_hash == $cms_cookie_hash and strlen($cms_cookie_hash) > 0) {
//			$cms_user_id = $cms_user;
//		} else {
//			$cms_user_id = "";
//		}
//		return $cms_user_id;
//	}


	public function log_out($is_admin_logout)
	{
		debug::on(false);
		debug::string('log_out()');
		debug::variable($is_admin_logout, 'is_admin_logout');

		$this->hooks->do_action('user_log_out', $is_admin_logout);
	}

//	public function log_out($is_admin_logout)
//	{
//		debug::on(false);
//		debug::variable($is_admin_logout, 'is_admin_logout');
//
//		if ($is_admin_logout)
//		{
//			$header_string = 'Set-Cookie: '.Config::get('cms.AdminTokenName').'=; Max-Age=-1; Path=/;';
//			$_COOKIE[Config::get('cms.AdminTokenName')] = '';
//		}
//		else
//		{
//			$header_string = 'Set-Cookie: '.Config::get('cms.UserTokenName').'=; Max-Age=-1; Path=/;';
//			$_COOKIE[Config::get('cms.UserTokenName')] = '';
//		}
//		header($header_string, false);
//
//		//--------------------------
//		// INTEGRATE STORE LOGOUT
//		//--------------------------
//		if ($is_admin_logout)
//		{
//			if (Config::get('cart.is_installed'))
//			{
//				$header_string = 'Set-Cookie: '.Config::get('cart.admin_token_name').'=; Max-Age=-1; Path=/;';
//				$_COOKIE[Config::get('cart.admin_token_name')] = '';
//				header($header_string, false);
//			}
//		}
//
//	}

	public function log_in($is_admin_login, $user_name, $user_password, $remember_me='N/A')
	{
		debug::on(false);
		debug::string('log_in()');
		debug::variable($is_admin_login, 'is_admin_login');
		debug::variable($user_name, 'user_name');
		debug::variable($user_password, 'user_password');
		debug::variable($remember_me, 'remember_me');

		$result = $this->hooks->do_action('user_log_in', $is_admin_login, $user_name, $user_password, $remember_me);
		debug::variable($result, 'result');

		$return_cms_result = false;
		if (is_array($result) && count($result) == 1)
		{
			if (isset($result[0]['class']) && $result[0]['class'] == 'Z9\Cms\CmsUserHook')
			{
				$return_cms_result = true;
			}
		}
		debug::variable($return_cms_result, 'return_cms_result');

		if ($return_cms_result)
		{
			return $result[0]['result'];
		}
		else
		{
			return $result;
		}
	}

//	public function log_in($is_admin_login, $UserName, $UserPassword, $RememberMe='N/A')
//	{
//		debug::on(false);
//		debug::variable($is_admin_login, 'is_admin_login');
//		debug::variable($UserName, 'UserName');
//		debug::variable($UserPassword, 'UserPassword');
//		debug::variable($RememberMe, 'RememberMe');
//
//		$is_login_success = false;
//
//		if (strlen($UserName) > 0 && strlen($UserPassword) > 0)
//		{
//			// User has submitted login information
//			// verify username and password, verify user is confirmed and active
//			$data = array(
//				'user_name' => $UserName,
//				'password' => $UserPassword,
//			);
//			debug::variable($data, 'data');
//			$rows = Gateway::_('Z9\Cms\Users')->verify_login($data);
//			debug::variable($rows, 'rows');
//
//			if (!empty($rows))
//			{
//				$is_login_success = true;
//				debug::variable($is_login_success, 'is_login_success');
//
//				//-----------------------------------------------
//				// if RememberMe, the set username in a cookie
//				//-----------------------------------------------
//				if ($is_admin_login)
//				{
//					setcookie(Config::get('cms.AdminLoginName'), $rows['UserName'], time()+30758400, "/");
//					debug::string("\$_COOKIE['".Config::get('cms.AdminLoginName')."']=".$rows['UserName']);
//				}
//				else
//				{
//					setcookie(Config::get('cms.UserLoginName'), $rows['UserName'], time()+30758400, "/");
//					debug::string("\$_COOKIE['".Config::get('cms.UserLoginName')."']=".$rows['UserName']);
//				}
//
//				//---------------------------------------------------
//				// Set the auth token so that the user is logged in
//				//---------------------------------------------------
//				if ($is_admin_login)
//				{
//					$cms_cookie_name = Config::get('cms.AdminTokenName');
//				}
//				else
//				{
//					$cms_cookie_name = Config::get('cms.UserTokenName');
//				}
//				debug::variable($cms_cookie_name, 'cms_cookie_name');
//				$cms_cookie_issued = Date::convert_unix_date(time(), $dateformat="yyyy-mm-dd-hh-mm-ss");
//				debug::variable($cms_cookie_issued, 'cms_cookie_issued');
//				$cms_cookie_expired = Date::convert_unix_date(time()+86400, $dateformat="yyyy-mm-dd-hh-mm-ss");
//				debug::variable($cms_cookie_expired, 'cms_cookie_expired');
//				$cms_public_part = $rows['uid'].":".$cms_cookie_issued.":".$cms_cookie_expired;
//				debug::variable($cms_public_part, 'cms_public_part');
//				$cms_calc_hash = md5(Config::get('cms.AuthSecretKey').":".md5($cms_public_part.":".Config::get('cms.AuthSecretKey')));
//				debug::variable($cms_calc_hash, 'cms_calc_hash');
//				$cms_auth_token = $cms_public_part.":".$cms_calc_hash;
//				debug::variable($cms_auth_token, 'cms_auth_token');
//
//				if (Config::get('cms.LogOutOnClose'))
//				{
//					setcookie($cms_cookie_name, $cms_auth_token, false, "/");
//				}
//				else
//				{
//					setcookie($cms_cookie_name, $cms_auth_token, time()+360000,"/");
//				}
//
//				debug::string("\$_COOKIE['".$cms_cookie_name."']=".$cms_auth_token);
//				//header('Set-Cookie: TestCookie=something+from+somewhere; Max-Age=3600; Domain=.www.domain.com; Path=/; secure;');
//				//$header_string = 'Set-Cookie: '.$cms_cookie_name.'='.$cms_auth_token.'; Max-Age=3600; Domain=.'.$_SERVER['HTTP_HOST'].'; Path=/; secure;';
//				//debug::variable($header_string, 'header_string');
//				//header($header_string);
//
//
//				//---------------------------------------------------------------
//				// Update the user's RememberMe, AuthToken, and LastLogin fields
//				//---------------------------------------------------------------
//				if ($RememberMe == 'N/A')
//				{
//					Gateway::_('Z9\Cms\Users')->update_last_login(array(
//						'auth_token' => $cms_auth_token,
//						'user_name' => $UserName,
//					));
//				}
//				else
//				{
//					$sqlRememberMe = 0;
//					if ($RememberMe == 1)
//					{
//						$sqlRememberMe = 1;
//					}
//					debug::variable($sqlRememberMe, 'sqlRememberMe');
//
//					Gateway::_('Z9\Cms\Users')->update_last_login(array(
//						'auth_token' => $cms_auth_token,
//						'remember_me' => $sqlRememberMe,
//						'user_name' => $UserName,
//					));
//				}
//
//				//--------------------------
//				// INTEGRATE STORE LOGIN
//				//--------------------------
//				if ($is_admin_login)
//				{
//					if (Config::get('cart.is_installed'))
//					{
//						debug::string('store login');
//
//						$data = array(
//							'password' => $UserPassword,
//							'user_name' => $UserName,
//						);
//						debug::variable($data, 'data');
//						$admin_row = Gateway::_('Z9\Cart\Users')->verify_login($data);
//						debug::variable($admin_row, 'admin_row');
//
//						if (is_array($admin_row))
//						{
//							if (!empty($admin_row['uid']))
//							{
//
//								//---------------------
//								// NEW AUTHENTICATION
//								//---------------------
//								$cms_cookie_name = Config::get('cart.admin_token_name');
//								$cms_cookie_issued = Date::convert_unix_date(time(), $dateformat="yyyy-mm-dd-hh-mm-ss");
//								$cms_cookie_expired = Date::convert_unix_date(time()+86400, $dateformat="yyyy-mm-dd-hh-mm-ss"); // 24 hours
//								$cms_public_part = $admin_row['uid'].":".$cms_cookie_issued.":".$cms_cookie_expired;
//								$cms_calc_hash = md5(Config::get('cart.auth_secret_key').":".md5($cms_public_part.":".Config::get('cart.auth_secret_key')));
//								$cms_auth_token = $cms_public_part.":".$cms_calc_hash;
//
//								if (Config::get('cart.log_out_on_close'))
//								{
//									setcookie($cms_cookie_name, $cms_auth_token, false, "/");
//								}
//								else
//								{
//									setcookie($cms_cookie_name, $cms_auth_token, time()+360000,"/"); // 100 hours
//								}
//
//								debug::string("\$_COOKIE['".$cms_cookie_name."']=".$cms_auth_token);
//
//							}
//						}
//					}
//				}
//
//			}
//		}
//
//		return $is_login_success;
//	}

}

?>