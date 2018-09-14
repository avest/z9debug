<?php
//===================================================================
// z9Debug
//===================================================================
// sql.tpl.php
// --------------------
// sql view file.
//
//       Date Created: 2018-01-14
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================
?>
<?php
$last_calling_file = '';
$last_calling_line = '';
$last_calling_function = '';
$last_calling_class = '';
?>

<?php if (is_array($sql_data)): ?>
	<?php $count = 0; ?>
	<?php $total_time = 0; ?>
	<?php $alt = false; ?>
	<?php foreach ($sql_data as $key => $query): ?>

		<?php
		$calling_file = $query['from_file'];
		$calling_line = $query['from_line'];
		$calling_function = $query['from_function'];
		$calling_class = $query['from_class'];

		$display_location = false;
		if ( $calling_file <> $last_calling_file ||
			$calling_function <> $last_calling_function ||
			$calling_class <> $last_calling_class
			)
		{
			$display_location = true;
		}
		?>

		<?php if ($display_location): ?>
			<div id=hr><div id=loc><?php echo $query['from_file']; ?>
			<?php if (!empty($query['from_class'])): ?>
				: <?php echo $query['from_class']; ?> -> <?php echo $query['from_function']; ?>
			<?php else: ?>
				<?php if (!empty($query['from_function'])): ?>
					: <?php $query['from_function']; ?>
				<?php endif; ?>
			<?php endif; ?>
			</div></div>
		<?php endif; ?>


		<table id=t><tr><td id=l><?php echo $query['from_line']; ?></td>
		<td id=m><?php if ((int)$query['total'] >= 1): ?><span style="color:red"><?php echo $query['total']; ?></span><?php else: ?><?php echo $query['total']; ?><?php endif; ?></td>
		<td id=v><div id=n><pre><?php echo htmlspecialchars($query['sql']); ?></pre></div></td>
		</tr></table>


		<?php if ($alt) { $alt = false; } else { $alt = true; } ?>
		<?php $count++; ?>

		<?php
		$last_calling_file = $query['from_file'];
		$last_calling_line = $query['from_line'];
		$last_calling_function = $query['from_function'];
		$last_calling_class = $query['from_class'];
		$total_time += $query['total'];
		?>

	<?php endforeach; ?>
<?php endif; ?>

<?php
$avg_time = 0;
if ($count > 0)
{
	$avg_time = $total_time / $count;
	$avg_time = number_format($avg_time, 4, '.', '');
}
?>
<br>
<table id=t><tr>
<td id=v>
Total Queries: <b><?php echo $count; ?></b> |
Total Time: <b><?php echo $total_time; ?></b> |
Avg Time: <b><?php echo $avg_time; ?></b> |
<?php if (isset($_POST['slow_queries']) && $_POST['slow_queries'] == '1'): ?>
<a href="javascript:show_sql();">All Queries</a>
<?php else: ?>
<a href="javascript:show_slow_queries();">Slow Queries</a>
<?php endif; ?>
</td>
</tr></table>
<br>

