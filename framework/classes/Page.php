<?php
//===================================================================
// Z9 Framework
//===================================================================
// Page.php
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
use Facade\Gateway;
use Z9\Framework\Hooks;
use Facade\Url;
use Facade\Dependency;



class Page
{
	/** @var Request  */
	private $request = null;
	/** @var User  */
	private $user = null;
	/** @var \Z9\Framework\Hooks  */
	private $hooks = null;

	public $data = array();

	public function __construct()
	{
		debug::on(false);
		$this->request = Dependency::inject('Request');
		$this->user = Dependency::inject('User');
		$this->hooks = Dependency::inject('\Z9\Framework\Hooks');
	}

	// full_url /test/my_page.id.100.htm?test=1&guid=xyz
	// url_parts = array() of 'scheme', 'host', 'port', 'user', 'pass', 'path', 'query', 'fragment'
	// query_string = test=1&guid=xyz
	// query_string_params = array('test' => 1, 'guid' => 'xyz')
	// url_path = /test/my_page.id.100.pg.2.htm
	// non_paging_url_path = /test/my_page.id.100.htm (remove .pg.XXXX. from url_path)
	// physical_file_path = /inetpub/site100/www/test/test.php
	// physical_file_exists = true/false
	// physical_file_ext = .php
	// resolved_web_path = /test/test.php
	// is_crawler
	// is_redirect_url = true/false
	// redirect = true/false
	// redirect_path = full url
	// redirect_code = 301

	public function init_data()
	{
		debug::on(false);
		debug::string('init()');

		$this->data['full_url'] = '';
		$this->data['url_parts'] = array();
		$this->data['query_string'] = '';
		$this->data['query_string_params'] = '';
		$this->data['url_path'] = '';
		$this->data['non_paging_url_path'] = '';
		$this->data['physical_file_path'] = '';
		$this->data['physical_file_exists'] = false;
		$this->data['physical_file_ext'] = '';
		$this->data['resolved_web_path'] = '';
		$this->data['is_crawler'] = false;
		$this->data['is_redirect_url'] = false;
		$this->data['redirect'] = false;
		$this->data['redirect_path'] = '';
		$this->data['redirect_code'] = 302;
		$this->data['headers'] = getallheaders();

		if (isset($this->request->_SERVER['REQUEST_URI']))
		{
			$this->data['full_url'] = $this->request->_SERVER['REQUEST_URI'];
			debug::variable($this->data['full_url'], 'this->data[full_url]');
		}

		$this->data['url_parts'] = parse_url($this->data['full_url']);
		debug::variable($this->data['url_parts'], 'this->data[url_parts]');

		if (isset($this->data['url_parts']['query']))
		{
			$this->data['query_string'] = $this->data['url_parts']['query'];
			debug::variable($this->data['query_string'], 'this->data[query_string]');
		}

		$this->data['query_string_params'] = Url::get_query_parameters($this->data['full_url']);
		debug::variable($this->data['query_string_params'], 'this->data[query_string_params]');

		// url with query string
		if (isset($this->data['url_parts']['path']))
		{
			$this->data['url_path'] = $this->data['url_parts']['path'];
			debug::variable($this->data['url_path'], 'this->data[url_path]');
		}

		//------------------------------------------------
		// Determine physical file path and name
		// if folder, then determine default file in folder
		//------------------------------------------------
		$this->data['physical_file_path'] = $_SERVER['DOCUMENT_ROOT'] . $this->data['url_path'];
		debug::variable($this->data['physical_file_path'], 'this->data[physical_file_path]');

		$this->data['physical_file_path'] = $this->set_default_file_path($this->data['physical_file_path']);
		debug::variable($this->data['physical_file_path'], 'this->data[physical_file_path]');

		$this->data['physical_file_exists'] = is_file($this->data['physical_file_path']);
		debug::variable($this->data['physical_file_exists'], 'this->data[physical_file_exists]');

		if ($this->data['physical_file_exists'])
		{
			$this->data['physical_file_ext'] = $this->get_file_extension($this->data['physical_file_path']);
			debug::variable($this->data['physical_file_ext'], 'this->data[physical_file_ext]');
		}

		$this->data['resolved_web_path'] = substr($this->data['physical_file_path'], strlen($_SERVER['DOCUMENT_ROOT']));
		debug::variable($this->data['resolved_web_path'], 'this->data[resolved_web_path]');

		$this->hooks->do_action('page_init_data');

		debug::variable($this->data, 'this->data');


	}

	//----------------------------------------------------------------------
	//  BACKEND
	// 	Name:  get_file_extension
	// 	Description:
	//		This function returns the file extension of a file name
	//  Input:  $cmsFileName
	//	Output: $file_ext
	//----------------------------------------------------------------------
	function get_file_extension($cmsFileName)
	{
		// find last period
		$last_period_pos = strrpos($cmsFileName, ".");
		//echo "last_period_pos=".$last_period_pos."<br>";
		if ($last_period_pos === false)
		{
			$file_ext = "";
		}
		else
		{
			$file_ext = substr($cmsFileName, $last_period_pos);
			//echo "file_ext=".$file_ext."<br>";
		}
		return $file_ext;
	}

	//----------------------------------------------------------------------
	//  BACKEND
	// 	Name:  set_default_file_path
	// 	Description:
	//		This function checks for an URL that doesn't have a file name,
	//		and returns a default file name if one exist in the given directory.
	//		Return the same path if a default file is not found
	//	Input:  file_path
	//	Output:  new_file_path
	//----------------------------------------------------------------------
	function set_default_file_path($file_path)
	{
		debug::on(false);
		debug::variable($file_path, 'file_path');

		debug::constant(Config::get('framework.default_pages'), 'framework.default_pages');

		if ($file_path == $_SERVER['DOCUMENT_ROOT'])
		{
			$file_path .= '/';
		}

		$new_file_path = $file_path;
		debug::variable($new_file_path, 'new_file_path');

		// if $strFilePath ends with "/" then look for a default page, such as index.php or index.htm
		$last_char = substr($file_path, strlen($file_path)-1, 1);
		debug::variable($last_char, 'last_char');

		if ($last_char == "/")
		{
			$default_path_found = false;
			foreach (Config::get('framework.default_pages') as $key => $default_page)
			{
				if (!$default_path_found)
				{
					if (file_exists($file_path.$default_page))
					{
						$new_file_path .= $default_page;
						$default_path_found = true;
					}
				}
			}
			debug::variable($new_file_path, 'new_file_path');
			debug::variable($default_path_found, 'default_path_found');
		}
		else
		{
			if (is_dir($file_path))
			{
				// if the trailing slash was not entered, redirect with the trailing slash
				debug::variable($_SERVER['DOCUMENT_ROOT'], '_SERVER[DOCUMENT_ROOT]');
				$document_root_len = strlen($_SERVER['DOCUMENT_ROOT']);
				debug::variable($document_root_len, 'document_root_len');
				$web_path = substr($file_path, $document_root_len);
				debug::variable($web_path, 'web_path');
				if (debug::is_on())
				{
					Http::no_cache();
					Http::location($web_path.'/');
					exit();
				}
				else
				{
					echo "Redirecting to ".$web_path."/"."<br>";
					exit();
				}
			}
		}
		return $new_file_path;
	}


}

?>