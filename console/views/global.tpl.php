<?php
//===================================================================
// z9Debug
//===================================================================
// global.tpl.php
// --------------------
// global view file.
//
//       Date Created: 2018-01-14
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

use Z9\Debug\Console\Facade\Value;

?>
	<?php if (is_array($global_data)): ?>
	 <?php foreach ($global_data as $global): ?>
		<?php $value_lines = Value::display_value_lines($global['value']); ?>
		<?php debug::variable($value_lines); ?>

		<?php $value_wrap = Value::display_value_wrap($global['type']); ?>
<table id=t><tr><td id=v><div id=n><?php echo $global['name']; ?></div> = (<?php echo $global['type']; ?>)<div id=q><?php echo $value_wrap.$value_lines.$value_wrap; ?></div></td></tr></table>
		<?php endforeach; ?>
	<?php endif; ?>

