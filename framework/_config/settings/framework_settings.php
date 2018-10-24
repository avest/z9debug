<?php

use Facade\Config;

Config::merge('framework', array(

	// list of default pages
	'default_pages' => array(
		0 => 'index.php',
		1 => 'index.htm',
		2 => 'index.html',
		3 => 'default.htm',
		4 => 'default.html',
		5 => 'default.php',
	),

));

// allow api calls without staging site authentication prompt
Config::merge_array('framework.bypass_staging_auth_urls',
	array(
));

?>