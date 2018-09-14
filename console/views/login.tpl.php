<?php
//===================================================================
// z9Debug
//===================================================================
// login.tpl.php
// --------------------
// login view file.
//
//       Date Created: 2005-04-23
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Debug Console</title>
	<link rel="stylesheet" href="<?php echo $web_root; ?>/console/css/stylesheet.css">
	<script type="text/javascript" src="<?php echo $web_root; ?>/console/js/console.js"></script>
</head>

<body>

<?php if (false): ?>
<header class="header">
	<div id=hdr>
		<table cellspacing=0 cellpadding=0 border=0 width=100%>
			<tr>
				<td align=left style="padding-top:3px;">
					<table cellspacing=0 cellpadding=0 border=0 style="display:inline;">
						<tr>
							<td id=nav>
							</td>
							<td>
								<div id=content_page_load_time></div>
							</td>
							<td>
								<div id=content_page_peak_memory></div>
							</td>
							<td>
								<div id=content_page_sql_time></div>
							</td>
						</tr>
					</table>

				</td>
				<td align=right>
					<div id=hide>
						<div class="close_btn"><a class=link_nodecor style="color:#ffffff;"
								href="javascript:window.close();">&#215;<!--&#9664;--></a></div>

					</div>
				</td>
			</tr>
		</table>
	</div>
</header>
<?php endif; ?>

<?php if (false): ?>
<div id="left_nav">
	<b>Debug</b><br>
</div>
<?php endif; ?>

<div id=login>
	<form style="padding:0px; margin:0px;" method="post" action="<?php echo $action; ?>">
		<input type="hidden" name="password_is_submitted" id="password_is_submitted" value="1" />
		<input type="hidden" name="redir" id="redir" value="<?php echo htmlspecialchars($redir); ?>" />
		<br>
		<br>
		<br>
		<br>
		<table cellspacing="0" cellpadding="4" border="0" width="250" align="center"
			style="background-color: #f9f9f9; border:1px solid #d0d0d0;">
			<tr>
				<td colspan="2" align="center" style="background-color:#f0f0f0;">
					<h1>Console</h1>
				</td>
			</tr>
			<?php //if (!empty(debug::get('remote_authentication'))): ?>
			<?php $remote_authentication = debug::get('remote_authentication'); ?>
			<?php if (!empty($remote_authentication)): ?>
			<tr>
				<td style="padding-top:20px; padding-left:20px;">
					Username:
				</td>
				<td style="padding-top:20px; padding-right:20px;">
					<input type="text" name="username" id="username" value="" />
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td style="padding-top:20px; padding-left:20px;">
					Password:
				</td>
				<td style="padding-top:20px; padding-right:20px;">
					<input type="password" name="password" id="password" value="" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td style="padding-bottom:20px;">
					<input class="button" type="submit" name="login_button" id="login_button" value="Login" />
				</td>
			</tr>
		</table>
	</form>
</div>

<script>
	document.getElementById("password").focus();
</script>

<?php if (false): ?>
<footer class="footer">
	<div id="ftrdiv">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr>
				<td align="left" class="ftrstatus">

				</td>
				<td align="right">
					z9Debug &nbsp;-&nbsp;
					v2.0.0 &nbsp;-&nbsp;
					Copyright &copy; 2015 &nbsp;-&nbsp;
					<a class=ftrlnk href="http://www.z9digital.com">z9Digital.com</a>
				</td>
			</tr>
		</table>
		<div>
</footer>
<?php endif; ?>

</body>
</html>