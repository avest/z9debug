<?php
//===================================================================
// z9Debug
//===================================================================
// GlobalAction.php
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

class GlobalDataAction
{
	public function get_global_data($data_dir)
	{
		debug::on(false);
		$file_path = $data_dir.DIRECTORY_SEPARATOR.'global_data.php';
		$data = File::read_file($file_path);
		$data = Str::remove_leading($data, "<?php exit(); ?>\n");
		$data = unserialize($data);
		return $data;
	}

}

?>