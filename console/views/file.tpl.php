<?php
//===================================================================
// z9Debug
//===================================================================
// file.tpl.php
// --------------------
// file view file.
//
//       Date Created: 2018-01-14
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

use Facade\File;

debug::on(false);

$div_id = 1;
?>
	<table class=filestbl cellspacing=0 cellpadding=3 border=0>
	<?php if (is_array($file_data['list'])): ?>
		<?php $count = 0; ?>
		<?php $alt = false; ?>
<?php foreach ($file_data['list'] as $file): ?>
	<?php debug::variable($file); ?>
<tr>
<td valign="top" class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo $count+1 ?></td>
	<td valign="top" class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo (isset($file['category'])) ? $file['category'] : ''; ?></td>
<td valign="top" class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo $file['name'] ?>

<?php if (is_array($file['functions_executed'])): ?>
<?php if (count($file['functions_executed']) > 0): ?>
<br>
<table cellspacing=0 cellpadding=0 border=0 width="100%">
<?php foreach ($file['functions_executed'] as $function => $function_info): ?>
	<?php debug::variable($function); ?>

<tr class="rowhover2">
	<td style="border-bottom:0; padding:0;padding-top:3px;padding-bottom:3px;padding-right:10px; width:40px; text-align:right;">
		<?php echo $function_info['count']; ?>
	</td>
	<td style="border-bottom:0;padding:0;padding-top:3px;padding-bottom:3px;"><?php echo $function_info['display_function']; ?></td>
	<td style="border-bottom:0; padding:0;padding-top:3px;padding-bottom:3px; padding-right:10px;text-align:right;">
		<?php if (!$function_info['toggled_on']): ?>
		<a href="javascript:void(0);" onclick="tx('<?php echo str_replace('\\', '\\\\', $_SERVER['DOCUMENT_ROOT'].$file['name']);?>', '<?php echo str_replace('\\', '\\\\', $function_info['namespace']); ?>', '<?php echo $function_info['class_name']; ?>', '<?php echo $function_info['function']; ?>', 'tx_<?php echo $div_id; ?>'); return false;">
			<div id="tx_<?php echo $div_id; ?>"><i class="fas fa-toggle-off" style="color:#a94442;"></i></div>
		</a>
		<?php else: ?>
		<a href="javascript:void(0);" onclick="tx('<?php echo str_replace('\\', '\\\\', $_SERVER['DOCUMENT_ROOT'].$file['name']);?>', '<?php echo str_replace('\\', '\\\\', $function_info['namespace']); ?>', '<?php echo $function_info['class_name']; ?>', '<?php echo $function_info['function']; ?>' , 'tx_<?php echo $div_id; ?>'); return false;">
			<div id="tx_<?php echo $div_id; ?>"><i class="fas fa-toggle-on" style="color:#3c763d"></i></div>
		</a>
		<?php endif; ?>

		<?php $div_id++; ?>
	</td>
</tr>
<?php endforeach; ?>
</table>
<?php endif; ?>
<?php endif; ?>

</td>
<td valign="top" class="<?php echo (($alt) ? 'alt' : '') ?>"><?php echo File::get_readable_file_size($file['size']) ?></td>
</tr>


<?php if ($alt) { $alt = false; } else { $alt = true; } ?>
<?php $count++; ?>
<?php endforeach; ?>
				<?php endif; ?>
	<tr>
		<td class="<?php echo (($alt) ? 'alt' : '') ?>">&nbsp;</td>
		<td class="<?php echo (($alt) ? 'alt' : '') ?>">&nbsp;</td>
		<td class="<?php echo (($alt) ? 'alt' : '') ?>"><b><?php echo File::get_readable_file_size($file_data['totals']['size']) ?></b></td>
	</tr>
	</table>

