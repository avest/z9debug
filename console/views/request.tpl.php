<?php
//===================================================================
// z9Debug
//===================================================================
// request.tpl.php
// --------------------
// request view file.
//
//       Date Created: 2018-01-14
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================
?>
<table class=filestbl cellspacing=0 cellpadding=3 border=0>
<?php //if (is_array($request_data) && !empty($request_id)): ?>
<?php if (is_array($request_data)): ?>
<?php $count = 0; ?>
<?php $alt = false; ?>
<?php foreach ($request_data as $request): ?>
<tr>
<td class="<?php echo (($alt) ? 'alt' : '') ?>" id="indicator"><?php echo ($request['request_id'] == $request_id) ? '&#9654;' : '' ?></td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo $count+1 ?></td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><a href="?z9dsid=<?php echo $session_id?>&z9drid=<?php echo $request['request_id']?>"><?php echo convert_unix_date($request['request_date'], 'mm/dd/yyyy hh:mm:ss') ?></a></td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo $request['request_url_path'] ?></td>
<td class="<?php echo (($alt) ? 'alt' : '') ?>"><a class="link" href="javascript:delete_request('<?php echo $request['request_id'];?>');">Delete</a></td>
</tr>
<?php if ($alt) { $alt = false; } else { $alt = true; } ?>
<?php $count++; ?>
<?php endforeach; ?>
<?php endif; ?>
</table>

<?php if ($count > 0): ?>
<br>
<a class="link" href="javascript:delete_request('ALL');">Delete All Requests</a>
<?php else: ?>
	<div class="content" style="padding:20px;">No requests found for this session.</div>
<?php endif; ?>
