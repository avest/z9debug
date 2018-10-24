<?php
//===================================================================
// z9Debug
//===================================================================
// toggle_breadcrumb.tpl.php
// --------------------
// toggle_breadcrumb view file.
//
//       Date Created: 2018-03-17
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

use Facade\Str;

?>
		&nbsp;/&nbsp;
<?php $is_first = true; ?>
<?php if (is_array($breadcrumb) && !empty($breadcrumb)): ?>
<?php foreach ($breadcrumb as $crumb): ?>
		<?php if (!$is_first): ?> &nbsp;/&nbsp; <?php endif; ?>
		<a href="#" onclick="ta('<?php echo str_replace("\\", "\\\\", $_SERVER['DOCUMENT_ROOT'].$crumb['path']); ?>');"><?php echo Str::html($crumb['name']); ?></a>
		<?php $is_first = false; ?>
<?php endforeach; ?>
<?php endif; ?>
