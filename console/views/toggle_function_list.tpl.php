<?php
//===================================================================
// z9Debug
//===================================================================
// toggle_function_list.tpl.php
// --------------------
// toggle_function_list view file.
//
//       Date Created: 2018-03-17
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================
?>
<?php debug::on(false); ?>

<table cellspacing=0 cellpadding=0 border=0 width="100%">

<?php $last_file_path = ''; ?>
<?php $last_class = ''; ?>
<?php $last_function = ''; ?>

<?php if (is_array($functions) && !empty($functions)): ?>
<?php foreach ($functions as $function): ?>
	<?php $curr_file_path = $function['file_path']; ?>
	<?php $curr_class = $function['class']; ?>
	<?php $curr_function = $function['function']; ?>

	<?php if ($curr_file_path <> $last_file_path): ?>
	<tr><td colspan=2>
		<?php //echo remove_leading($curr_file_path, $_SERVER['DOCUMENT_ROOT']); ?>
		<?php if (!empty($curr_class)): ?>
			<?php echo $curr_class; ?>
		<?php endif; ?>
	</td></tr>
	<?php endif; ?>

	<tr class="rowhover" onclick="toggle('<?php echo str_replace("\\", "\\\\", $function['file_path']);?>', '<?php echo str_replace('\\', '\\\\', $function['namespace']); ?>', '<?php echo $function['class']; ?>', '<?php echo $function['function']; ?>');">
		<td style="padding-left:5px;">
		<?php if (!empty($function['function'])): ?>
				<span class="line"><?php echo $function['line_number']; ?></span> <span class="vert">|</span> <?php echo $function['function']; ?>()
		<?php else: ?>
				[file]
		<?php endif; ?>
		</td>
		<td align="right" style="padding-right:5px;">
			<?php if ($function['is_on']): ?>
			<i class="fas fa-toggle-on" style="color:#3c763d;"></i>
			<?php else: ?>
			<i class="fas fa-toggle-off" style="color:#a94442;"></i>
			<?php endif; ?>
		</td>
	</tr>

	<?php $last_file_path = $curr_file_path; ?>
	<?php $last_class = $curr_class; ?>
	<?php $last_function = $curr_function; ?>
<?php endforeach; ?>
<?php endif; ?>

</table>
