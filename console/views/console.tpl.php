<?php
//===================================================================
// z9Debug
//===================================================================
// console.tpl.php
// --------------------
// console view file.
//
//       Date Created: 2005-04-23
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

use Facade\Date;
use Facade\File;

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Debug Console</title>
	<link rel="stylesheet" href="<?php echo $web_root; ?>/console/css/stylesheet.css?v=<?php echo time(); ?>">
	<link rel="shortcut icon" href="<?php echo $web_root; ?>/console/images/wrench.ico">
	<script type="text/javascript" src="<?php echo $web_root; ?>/console/js/console.js?v=<?php echo time(); ?>"></script>
	<link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">

	<script>
	function show_error(message)
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_var').style.display = 'block';

		var x = document.getElementById('content_var');

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'message': message },
				'url':'error.php',
				'onSuccess':function(req){ document.getElementById('content_var').innerHTML = req.responseText; }
			}
		);
	}

	function show_var(page)
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_var').style.display = 'block';

		//alert(page);

		var x = document.getElementById('content_var');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'page': page, 'session_id': '<?php echo $session_id; ?>', 'request_id': '<?php echo $request_id; ?>' },
				'url':'page.php',
				'onSuccess':function(req){ document.getElementById('content_var').innerHTML = req.responseText; }
			}
		);
	}

	function show_request()
	{
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_request').style.display = 'block';

		var x = document.getElementById('content_request');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'session_id': '<?php echo $session_id; ?>' },
				'url':'request.php',
				'onSuccess':function(req){ document.getElementById('content_request').innerHTML = req.responseText; }
			}
		);
	}

	function delete_request(request_id)
	{
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_request').style.display = 'block';

		var x = document.getElementById('content_request');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'delete': 1, 'session_id': '<?php echo $session_id; ?>', 'request_id': request_id },
				'url':'request.php',
				'onSuccess':function(req){ document.getElementById('content_request').innerHTML = req.responseText; }
			}
		);
	}

	function show_session()
	{
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_session').style.display = 'block';

		var x = document.getElementById('content_session');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{  },
				'url':'session.php',
				'onSuccess':function(req){ document.getElementById('content_session').innerHTML = req.responseText; }
			}
		);
	}

	function delete_session(session_id)
	{
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_session').style.display = 'block';

		var x = document.getElementById('content_session');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'delete': 1, 'session_id': session_id },
				'url':'session.php',
				'onSuccess':function(req){ document.getElementById('content_session').innerHTML = req.responseText; }
			}
		);
	}

	function show_timing()
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_timing').style.display = 'block';
	}

	function show_sql()
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_sql').style.display = 'block';

		var x = document.getElementById('content_sql');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'session_id': '<?php echo $session_id; ?>', 'request_id': '<?php echo $request_id; ?>' },
				'url':'sql.php',
				'onSuccess':function(req){ document.getElementById('content_sql').innerHTML = req.responseText; }
			}
		);
	}

	function show_slow_queries()
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_sql').style.display = 'block';

		var x = document.getElementById('content_sql');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'session_id': '<?php echo $session_id; ?>', 'request_id': '<?php echo $request_id; ?>', 'slow_queries': 1 },
				'url':'sql.php',
				'onSuccess':function(req){ document.getElementById('content_sql').innerHTML = req.responseText; }
			}
		);
	}

	function show_file()
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_file').style.display = 'block';

		var x = document.getElementById('content_file');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'session_id': '<?php echo $session_id; ?>', 'request_id': '<?php echo $request_id; ?>' },
				'url':'file.php',
				'onSuccess':function(req){ document.getElementById('content_file').innerHTML = req.responseText; }
			}
		);
	}

	function show_global()
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_global').style.display = 'block';

		var x = document.getElementById('content_global');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'session_id': '<?php echo $session_id; ?>', 'request_id': '<?php echo $request_id; ?>' },
				'url':'global.php',
				'onSuccess':function(req){ document.getElementById('content_global').innerHTML = req.responseText; }
			}
		);
	}

	function show_cms()
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_cms').style.display = 'block';

		var x = document.getElementById('content_cms');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'session_id': '<?php echo $session_id; ?>', 'request_id': '<?php echo $request_id; ?>' },
				'url':'cms.php',
				'onSuccess':function(req){ document.getElementById('content_cms').innerHTML = req.responseText; }
			}
		);
	}

	function show_settings()
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'none';
		document.getElementById('header_div').style.display = 'block';
		document.getElementById('content_settings').style.display = 'block';

		var x = document.getElementById('content_settings');
		x.innerHTML = '<span class="loading">loading...</span>';

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{  },
				'url':'settings.php',
				'onSuccess':function(req){ document.getElementById('content_settings').innerHTML = req.responseText; }
			}
		);
	}

	function show_toggle()
	{
		document.getElementById('content_session').style.display = 'none';
		document.getElementById('content_request').style.display = 'none';
		document.getElementById('content_var').style.display = 'none';
		document.getElementById('content_timing').style.display = 'none';
		document.getElementById('content_sql').style.display = 'none';
		document.getElementById('content_file').style.display = 'none';
		document.getElementById('content_global').style.display = 'none';
		document.getElementById('content_cms').style.display = 'none';
		document.getElementById('content_settings').style.display = 'none';
		document.getElementById('header_div').style.display = 'none';
		document.getElementById('content_toggle').style.display = 'block';

		var toggle_breadcrumb_div = document.getElementById('toggle_breadcrumb');
		toggle_breadcrumb_div.innerHTML = '<span class="loading">loading...</span>';

		var toggle_dir_list_div = document.getElementById('toggle_dir_list');
		toggle_dir_list_div.innerHTML = '<span class="loading">loading...</span>';

		var toggle_file_list_div = document.getElementById('toggle_file_list');
		toggle_file_list_div.innerHTML = '<span class="loading">loading...</span>';

		var toggle_function_list_div = document.getElementById('toggle_function_list');
		toggle_function_list_div.innerHTML = '<span class="loading">loading...</span>';

		ta('<?php echo str_replace("\\", "\\\\", $physical_dir); ?>');
		to();

	}

	function ta(physical_path)
	{
		tb(physical_path);
		td(physical_path);
		tf(physical_path);
		tz(physical_path);
	}

	// update toggle breadcrumb
	function tb(physical_path)
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'physical_path': physical_path },
				'url':'toggle_breadcrumb.php',
				'onSuccess':function(req){ document.getElementById('toggle_breadcrumb').innerHTML = req.responseText; }
			}
		);
	}

	// update toggle dir list
	function td(physical_path)
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'physical_path': physical_path },
				'url':'toggle_dir_list.php',
				'onSuccess':function(req){ document.getElementById('toggle_dir_list').innerHTML = req.responseText; }
			}
		);
	}

	// update toggle file list
	function tf(physical_path)
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'physical_path': physical_path },
				'url':'toggle_file_list.php',
				'onSuccess':function(req){ document.getElementById('toggle_file_list').innerHTML = req.responseText; }
			}
		);
	}

	// udpate toggle function list
	function tz(physical_path)
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'physical_path': physical_path },
				'url':'toggle_function_list.php',
				'onSuccess':function(req){ document.getElementById('toggle_function_list').innerHTML = req.responseText; }
			}
		);
	}

	// udpate toggle on list
	function to()
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{  },
				'url':'toggle_on_list.php',
				'onSuccess':function(req){ document.getElementById('toggle_on_list').innerHTML = req.responseText; }
			}
		);
	}

	// toggle just the toggle on/off character
	function tx(file_path, namespace, class_, function_, char_div)
	{
		//alert(char_div);
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'file_path': file_path, 'namespace': namespace, 'class': class_, 'function': function_ },
				'url':'toggle.php',
				'onSuccess':function(req){
					//alert('z'+req.responseText+'z');
					if (req.responseText == '1')
					{
						//alert('1');
						document.getElementById(char_div).innerHTML = '<i class="fas fa-toggle-on" style="color:#3c763d"></i>';
					}
					else if (req.responseText == '0')
					{
						//alert('0');
						document.getElementById(char_div).innerHTML = '<i class="fas fa-toggle-off" style="color:#a94442;"></i>';
					}
				}
			}
		);

		show_on_off_count();
	}

	function toggle(file_path, namespace, class_, function_)
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'file_path': file_path, 'namespace': namespace, 'class': class_, 'function': function_ },
				'url':'toggle.php',
				'onSuccess':function(req){
					tz(file_path); // toggle function list
					to(); // toggle on list
					show_on_off_count();
				}
			}
		);
	}

	function toggle_force_enabled()
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'force_enabled': '1' },
				'url':'toggle.php',
				'onSuccess':function(req){
					show_menu_settings();
				}
			}
		);
	}

	function toggle_force_suppress_output()
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'force_suppress_output': '1' },
				'url':'toggle.php',
				'onSuccess':function(req){
					show_menu_settings();
				}
			}
		);
	}

	function show_menu_settings()
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{  },
				'url':'menu_settings.php',
				'onSuccess':function(req){
					document.getElementById('menu_settings').innerHTML = req.responseText;
				}
			}
		);
	}

	function show_on_off_count()
	{
		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{  },
				'url':'on_off_count.php',
				'onSuccess':function(req){
					document.getElementById('on_off_count').innerHTML = req.responseText;
				}
			}
		);
	}

	// more link
	function m(page, var_data_key, lines_key)
	{
		//alert(var_data_key+' '+lines_key);

		var x = document.getElementsByClassName('mo_'+var_data_key+'_'+lines_key);
		var i;
		for (i = 0; i < x.length; i++)
		{
			x[i].innerHTML = 'working...';
		}

		AjaxRequest.get(
			{
				'method':'POST',
				'parameters':{ 'page': page, 'var_data_key': var_data_key, 'lines_key': lines_key, 'session_id': '<?php echo $session_id; ?>', 'request_id': '<?php echo $request_id; ?>' },
				'url':'more.php',
				'onSuccess':function(req){ document.getElementById('m_'+var_data_key+'_'+lines_key).innerHTML = req.responseText; }
			}
		);

	}

	</script>


	<script type="text/javascript" src="<?php echo $web_root; ?>/console/js/ajaxRequest_compact.js"></script>
</head>

<body>

<?php // Header ?>
<div id=header_div>
<header class="header">
	<div id=hdr>
		<table cellspacing=0 cellpadding=0 border=0 width=100%>
		<tr>
			<td align=left style="padding-top:3px;">
				<table cellspacing=0 cellpadding=0 border=0 style="display:inline;"><tr>
				<td style="font-size:12px;">
					<?php echo Date::convert_unix_date($request_date, 'mm/dd/yyyy hh:mm:ss'); ?><?php echo (!empty($request_url_path)) ? ':' : ''; ?>
					<?php echo $request_url_path; ?>
				</td>
				</tr></table>

			</td>
			<td align=right>
				<?php if (false): ?>
				<div id=hide>
				<div class="close_btn"><a class=link_nodecor style="color:#ffffff;" href="javascript:window.close();">&#215;<!--&#9664;--></a></div>
				</div>
				<?php endif; ?>
			</td>
		</tr>
		</table>
	</div>
</header>
</div>

<div id="left_nav">
	<b>Debug</b><br>
	<a class=link href="?z9dsid=<?php echo $session_id; ?>&latest_request=1">Latest</a><br>
	<a class=link href="javascript:show_session();">Session</a><br>
	<a class=link href="javascript:show_request();">Request</a><br>
	<a class=link href="javascript:show_var(1);">Var / Str</a><br>

	<?php if ($var_data_page_count > 1): ?>
	<?php $br_count = 0; ?>
	<?php for ($i = 1; $i <= $var_data_page_count; $i++ ): ?>
	<a class=link href="javascript:show_var(<?php echo $i; ?>);">[<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>]</a>
		<?php $br_count++; ?>
		<?php if ($br_count == 5): ?>
			<br>
			<?php $br_count = 0; ?>
		<?php endif; ?>
	<?php endfor; ?>
	<?php if ($br_count > 0): ?>
	<br>
	<?php endif; ?>
	<?php endif; ?>

	<?php if (false): ?>
	<!-- future -->
	<a class=link href="javascript:show_timing();">Timer</a><br>
	<?php endif; ?>
	<a class=link href="javascript:show_sql();">SQL</a><br>
	<a class=link href="javascript:show_file();">File</a><br>
	<a class=link href="javascript:show_global();">Global</a><br>
	<?php if ($is_cms_installed): ?>
	<a class=link href="javascript:show_cms();">CMS</a><br>
	<?php endif; ?>
	<a class=link href="javascript:show_toggle();">On / Off</a> <div style="display:inline;" id="on_off_count"></div><br>
	<?php if (false): // not yet implemented ?>
	<a class=link href="javascript:show_settings();">Settings</a><br>
	<?php endif; ?>
	<a class=link href="?logout=1">Log Out</a>
	<br>
	<br>


	<div id="menu_settings">
	</div>

</div>

<?php // Session ?>
<div id=content_session></div>

<?php // Request ?>
<div id=content_request></div>

<?php // Var / Str ?>


<div id=content_var></div>

<?php // Timer ?>
<div id=content_timing></div>

<?php // SQL ?>
<div id=content_sql></div>

<?php // File ?>
<div id=content_file></div>

<?php // Global ?>
<div id=content_global></div>

<?php // CMS ?>
<div id=content_cms></div>

<?php // Settings ?>
<div id=content_settings></div>


<?php // Toggle ?>
<div id=content_toggle>

	<div style="padding:20px; padding-top:10px; padding-bottom:10px; border-bottom:2px solid #cccccc;">
		<div id="toggle_breadcrumb" style="padding:20px; padding-top:10px; padding-bottom:10px; margin-bottom:5px; background-color:#f0f0f0; border-radius:5px;"></div>

		<table cellspacing=0 cellpadding=0 border=0 width="100%">
		<tr>
			<td width="30%" valign="top">
				<div id="toggle_dir_list" style="padding:20px; padding-top: 10px; padding-left: 0; line-height:1.7em; overflow-y: scroll; height: 300px;"></div>
			</td>

			<td width="30%" valign="top">
				<div id="toggle_file_list" style="padding:20px; padding-top:10px; line-height:1.7em; overflow-y: scroll; height: 300px;"></div>
			</td>

			<td width="40%" valign="top">
				<div id="toggle_function_list" style="padding:20px; padding-top: 10px; line-height:1.7em; overflow-y: scroll; height: 300px;"></div>

			</td>
		</tr>
		</table>
	</div>

	<div style=";position:fixed; left: 150px; top: 405px; right:20px; bottom:65px;">
	<div style="overflow-y: scroll; padding:20px; padding-top:10px; height:100%;">
		<div id="toggle_on_list" style="line-height:1.7em;">
			test
		</div>
	</div>
	</div>

</div>



<?php // Footer ?>
<footer class="footer">
	<div id="ftrdiv">
		<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr>
		<td align="left" class="ftrstatus">
<?php if (false): ?>
		<?php echo Date::convert_unix_date($request_date, 'mm/dd/yyyy hh:mm:ss'); ?>:
		<?php echo $request_url_path; ?>
<?php endif; ?>

			<div id=content_page_load_time>page load time: <b><?php echo $page_load_time; ?></b></div>
			<div id=content_page_peak_memory>peak memory: <b><?php echo File::friendly_file_size($page_peak_memory); ?></b></div>
			<div id=content_page_sql_time>sql query time: <b><?php echo $page_sql_time; ?></b></div>


		</td>
		<td align="right">
			<a class=ftlnk target="_blank" href="http://www.z9digital.com/z9debug">Z9 Debug</a>
		</td>
		</tr></table>
	<div>
</footer>

</body>
</html>

<?php if (empty($session_id)): ?>
<script>
show_error('Session not found.');
</script>
<?php elseif (empty($request_id)): ?>
<script>
show_error('Request not found.');
</script>
<?php else: ?>
<script>
show_var(1);
</script>
<?php endif; ?>

<script>
show_menu_settings();
show_on_off_count();
</script>

