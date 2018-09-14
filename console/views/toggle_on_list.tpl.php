<?php
//===================================================================
// z9Debug
//===================================================================
// toggle_on_list.tpl.php
// --------------------
// toggle_on_list view file.
//
//       Date Created: 2018-03-17
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================
?>
<?php debug::on(false); ?>

<?php $last_file_path = ''; ?>
<?php $last_class = ''; ?>
<?php $last_function = ''; ?>

<table cellspacing=0 cellpadding=0 border=0>

<?php if (is_array($force_on)): ?>
	<?php foreach ($force_on as $on_file_path => $on_file_functions): ?>
		<?php debug::variable($on_file_path); ?>
		<?php debug::variable($on_file_functions); ?>

		<?php $curr_file_path = $on_file_path; ?>
		<?php $curr_namespace = ''; ?>
		<?php $curr_class = ''; ?>
		<?php $curr_function = ''; ?>

		<?php if (is_array($on_file_functions)): ?>
			<?php foreach ($on_file_functions as $on_file_function): ?>

				<?php if (in_str($on_file_function, '::')): ?>
					<?php list($curr_class, $curr_function) = explode('::', $on_file_function); ?>
				<?php else: ?>
					<?php $curr_class = ''; ?>
					<?php $curr_function = $on_file_function; ?>
				<?php endif; ?>

				<?php if (in_str($curr_class, '/')): ?>
					<?php $curr_namespace = dirname($curr_class); ?>
					<?php if ($curr_namespace == '/'): ?>
						<?php $curr_namespace = ''; ?>
					<?php endif; ?>
					<?php $curr_class = basename($curr_class); ?>
				<?php endif; ?>

				<?php debug::variable($curr_namespace); ?>
				<?php debug::variable($curr_class); ?>
				<?php debug::variable($curr_function); ?>

				<tr>
					<td style="padding-right:10px;">
						<a href="#" onclick="ta('<?php echo str_replace('\\', '\\\\', $_SERVER['DOCUMENT_ROOT'].$curr_file_path); ?>')"><?php echo remove_leading($curr_file_path, $_SERVER['DOCUMENT_ROOT']); ?></a>

						<?php if (!empty($curr_namespace) || !empty($curr_class)): ?>
							 :
						<?php endif; ?>

						<?php echo $curr_namespace; ?>

						<?php if (!empty($curr_class)): ?>
							<?php echo basename($curr_class); ?> ->
						<?php endif; ?>

						<?php if (!empty($curr_function)): ?>
							<?php if ($curr_function <> '-'): ?>
								<?php echo $curr_function; ?>()
							<?php else: ?>
								<?php echo '[file]'; ?>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<td align=right>
						<a href="#" onclick="toggle('<?php echo str_replace('\\', '\\\\', $_SERVER['DOCUMENT_ROOT'].$curr_file_path);?>', '<?php echo str_replace('\\', '\\\\', $curr_namespace); ?>', '<?php echo $curr_class; ?>', '<?php echo $curr_function; ?>')">
						<i class="fas fa-toggle-on" style="color:#3c763d"></i>
						</a>
					</td>
				</tr>



				<?php $last_namespace = $curr_namespace; ?>
				<?php $last_class = $curr_class; ?>
				<?php $last_function = $curr_function; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php $last_file_path = $curr_file_path; ?>
	<?php endforeach; ?>
<?php endif; ?>
</table>
