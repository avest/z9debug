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

class CmsDataAction
{
	public function get_cms_data($data_dir)
	{
		debug::on(false);
		debug::variable($data_dir);
		$file_path = $data_dir.DIRECTORY_SEPARATOR.'cms_data.php';
		debug::variable($file_path);
		$data = File::read_file($file_path);
		$data = Str::remove_leading($data, "<?php exit(); ?>\n");
		$data = unserialize($data);
		return $data;
	}

}

?>