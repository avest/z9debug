<?php
//===================================================================
// z9Debug
//===================================================================
// toggle_file_list.tpl.php
// --------------------
// toggle_file_list view file.
//
//       Date Created: 2018-03-17
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

use Facade\Str;

?>
<?php if (!empty($dir_file_list)): ?>
<?php if (is_array($dir_file_list)): ?>
<?php foreach ($dir_file_list as $key => $file): ?>
	<?php $display_file = $file; ?>
	<?php //$display_file = str_replace('_', ' ', $display_file); ?>
	<?php //$display_file = str_replace('-', ' ', $display_file); ?>
		<?php if (true): ?>
		<nobr><a href="#" onclick="ta('<?php echo str_replace("\\", "\\\\", $dir_path.$file); ?>');"><i class="far fa-file"></i>&nbsp;
				<?php echo Str::html($display_file); ?></a></nobr><br>
		<?php endif; ?>
		<?php if (false): ?>
		<nobr><a href="#" onclick="ta('<?php echo str_replace("\\", "\\\\", $dir_path.$file); ?>');"><?php echo Str::html(Str::remove_trailing($display_file, '.php')); ?></a></nobr><br>
		<?php endif; ?>

<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>

