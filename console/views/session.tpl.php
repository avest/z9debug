<?php
//===================================================================
// z9Debug
//===================================================================
// session.tpl.php
// --------------------
// session view file.
//
//       Date Created: 2018-01-14
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================
?>
<table class=filestbl cellspacing=0 cellpadding=3 border=0>
<?php //if (is_array($session_data) && !empty($session_id)): ?>
<?php if (is_array($session_data)): ?>
<?php $count = 0; ?>
<?php $alt = false; ?>
<?php foreach ($session_data as $session): ?>
<tr>
<td class="<?php echo (($alt) ? 'alt' : '') ?>" id="indicator"><?php echo ($session['session_id'] == $session_id) ? '&#9654;' : '' ?></td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo $count+1 ?></td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><a href="?z9dsid=<?php echo $session['session_id']?>"><?php echo convert_unix_date($session['session_date'], 'mm/dd/yyyy hh:mm:ss') ?></a></td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo $session['request_count']?> &nbsp;request</td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo $session['session_name']?></td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><a class="link" href="javascript:delete_session('<?php echo $session['session_id']; ?>');">Delete</a></td>
</tr>
<?php if ($alt) { $alt = false; } else { $alt = true; } ?>
<?php $count++; ?>
<?php endforeach; ?>
<?php endif; ?>
</table>

<?php if ($count > 0): ?>
<br>
&nbsp;<a class="link" href="javascript:delete_session('ALL');">Delete All Sessions</a>
<?php endif; ?>


