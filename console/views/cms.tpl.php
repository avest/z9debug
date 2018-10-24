<?php
//===================================================================
// z9Debug
//===================================================================
// cms.tpl.php
// --------------------
// cms view file.
//
//       Date Created: 2018-01-14
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

use Z9\Debug\Console\Facade\Value;
?>
	<?php if (is_array($cms_data)): ?>
	 <?php foreach ($cms_data as $cms): ?>
		<?php $value_lines = Value::display_value_lines($cms['value']); ?>
		<?php debug::variable($value_lines); ?>

		<?php $value_wrap = Value::display_value_wrap($cms['type']); ?>
<table id=t><tr><td id=v><div id=n><?php echo $cms['name']; ?></div> = (<?php echo $cms['type']; ?>)<div id=q><?php echo $value_wrap.$value_lines.$value_wrap; ?></div></td></tr></table>
		<?php endforeach; ?>
	<?php endif; ?>

