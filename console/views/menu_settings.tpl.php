<?php
//===================================================================
// z9Debug
//===================================================================
// menu_settings.tpl.php
// --------------------
// menu settings view file.
//
//       Date Created: 2018-03-18
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================
?><?php debug::on(false); ?>

<table cellspacing=0 cellpadding=0 border=0>
<tr>
	<td align="center" style="padding-bottom:10px;">Enable<br>
	<?php if ($force_enabled): ?>
		<a href="#" onclick="toggle_force_enabled();"><i class="fas fa-toggle-on" style="color:#3c763d;"></i></a>
	<?php else: ?>
		<a href="#" onclick="toggle_force_enabled();"><i class="fas fa-toggle-off" style="color:#a94442;"></i></a>
	<?php endif; ?>
	</td>
</tr>
<tr>
	<td align="center" style="padding-bottom:10px;">Suppress<br>
	<?php if ($force_suppress_output): ?>
		<a href="#" onclick="toggle_force_suppress_output();"><i class="fas fa-toggle-on" style="color:#3c763d;"></i></a>
	<?php else: ?>
		<a href="#" onclick="toggle_force_suppress_output();"><i class="fas fa-toggle-off" style="color:#a94442;"></i></a>
	<?php endif; ?>
	</td>
</tr>
</table>
<br>
