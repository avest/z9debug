<?php
//===================================================================
// z9Debug
//===================================================================
// toggle_dir_list.tpl.php
// --------------------
// toggle_dir_list view file.
//
//       Date Created: 2018-03-17
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

use Facade\Str;

?>
<?php if ($physical_path <> $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR): ?>
	<a href="#" onclick="ta('<?php echo str_replace("\\", "\\\\", dirname($physical_path.DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR); ?>');"><i class="fas fa-caret-right"></i>
	<i class="far fa-folder"></i>&nbsp;
	..</a><br>
<?php endif; ?>

<?php if (!empty($dir_dir_list)): ?>
<?php if (is_array($dir_dir_list)): ?>
<?php foreach ($dir_dir_list as $key => $dir): ?>
	<?php $display_dir = $dir; ?>
	<?php //$display_dir = str_replace('_', ' ', $display_dir); ?>
	<?php //$display_dir = str_replace('-', ' ', $display_dir); ?>
		<a href="#" onclick="ta('<?php echo str_replace("\\", "\\\\", $physical_path.$dir.DIRECTORY_SEPARATOR); ?>');"><i class="fas fa-caret-right"></i>
		<i class="far fa-folder"></i>&nbsp;
		<?php echo Str::html($display_dir); ?></a><br>
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>
