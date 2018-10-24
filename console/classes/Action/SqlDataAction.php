<?php
//===================================================================
// z9Debug
//===================================================================
// SqlDataAction.php
// --------------------
//
//       Date Created: 2018-10-22
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================


namespace Z9\Debug\Console\Action;

use debug;
use Facade\File;
use Facade\Str;

class SqlDataAction
{
	public function get_sql_data($data_dir)
	{
		debug::on(false);
		$file_path = $data_dir.DIRECTORY_SEPARATOR.'sql_data.php';
		$data = File::read_file($file_path);
		$data = Str::remove_leading($data, "<?php exit(); ?>\n");
		$data = unserialize($data);

		if (is_array($data))
		{
			foreach ($data as $key => $query)
			{
				$data[$key]['sql'] = $this->clean_sql($query['sql']);
				$data[$key]['from_class'] = $this->clean_class($query['from_class']);
			}
		}

		if (isset($_POST['slow_queries']) && $_POST['slow_queries'] == '1')
		{
			if (is_array($data))
			{
				foreach ($data as $key => $query)
				{
					if ((int)$query['total'] < 1)
					{
						unset($data[$key]);
					}
				}
			}
		}


		return $data;
	}

	private function clean_class($class)
	{
		$last_pos = strrpos($class, '\\');
		if ($last_pos !== false)
		{
			$class = substr($class, $last_pos+1);
		}
		return $class;
	}

	private function clean_sql($sql)
	{
		debug::on(false);
		$lines = explode("\n", $sql);
		debug::variable($lines);
		$min_tab_count = 9999;
		if (is_array($lines))
		{
			foreach ($lines as $line_key => $line)
			{
				$empty_line = trim($line);
				if (empty($empty_line))
				{
					unset($lines[$line_key]);
				}
				else
				{
					$tabs = explode("\t", $line);
					debug::variable($tabs);
					$tab_count = 0;
					if (is_array($tabs))
					{
						foreach ($tabs as $tab_key => $tab)
						{
							if (empty($tab))
							{
								$tab_count++;
							}
							else
							{
								break;
							}
						}
					}
					if ($tab_count < $min_tab_count)
					{
						$min_tab_count = $tab_count;
					}
				}
			}
		}
		//echo "min_tab_count=".$min_tab_count."<br>";

		if ($min_tab_count <> 9999 && $min_tab_count <> 0)
		{
			if (is_array($lines))
			{
				foreach ($lines as $line_key => $line)
				{
					$lines[$line_key] = substr($line, $min_tab_count);
				}
			}
		}

		$return = implode("\n", $lines);
		return $return;
	}

}

?>