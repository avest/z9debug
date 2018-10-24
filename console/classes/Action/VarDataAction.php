<?php
//===================================================================
// z9Debug
//===================================================================
// CmsAction.php
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

class VarDataAction
{
	public function get_var_data($data_dir, $page=1)
	{
		debug::on(false);
		$file_path = $data_dir.DIRECTORY_SEPARATOR.'var_data.php';
		//$data = read_file($file_path);
		//$data = unserialize($data);

		$data = array();
		$line_count = 1;
		if (is_file($file_path))
		{
			if ($file = fopen($file_path, "r"))
			{
				while(!feof($file))
				{
					$line = fgets($file);
					// skip first line...
					if ($line <> "<?php exit(); ?>\n")
					{
						if (!empty($line) && $line_count == $page)
						{
							$unserialize_line = unserialize($line);
							if (is_array($unserialize_line))
							{
								$data = array_merge($data, $unserialize_line);
							}
						}
						$line_count++;
					}
				}
				fclose($file);
			}
		}
		unset($line);

		return $data;
	}

}

?>