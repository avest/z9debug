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
					<input style="padding:5px;" type="text" name="username" id="username" value="" />
				</td>
			</tr>
			<?php endif; ?>
			<tr>
				<td style="padding-top:20px; padding-left:20px;">
					Password:
				</td>
				<td style="padding-top:20px; padding-right:20px;">
					<input style="padding:5px;" type="password" name="password" id="password" value="" />
				</td>
			</tr>
			<tr>
				<td></td>
				<td style="padding-bottom:20px;padding-top:15px;">
					<input class="button" type="submit" name="login_button" id="login_button" value="Login" />
				</td>
			</tr>
			<?php if (false): ?>
			<tr>
				<td colspan="2" style="padding-left:20px;"><center>
					<b>Strong Password Required</b><br>&nbsp;
					</center>
				</td>
			</tr>
			<?php endif; ?>
		</table>
	</form>
</div>

<script>
	document.getElementById("password").focus();
</script>

</body>
</html>