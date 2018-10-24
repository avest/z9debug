<?php
//===================================================================
// z9Debug
//===================================================================
// PageAction.php
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

class PageDataAction
{
	public function get_page_data($request_dir)
	{
		debug::on(false);
		$file_path = $request_dir.DIRECTORY_SEPARATOR.'page_data.php';
		$data = File::read_file($file_path);
		$data = Str::remove_leading($data, "<?php exit(); ?>\n");
		$data = unserialize($data);
		return $data;
	}
}

?>