<?php

use Facade\Config;

//-----------------
// GLOBALS
//-----------------

Config::set('site', array(

	'framework_dir' => APP_ROOT_DIR.'/framework/',

	// app config dirs to load
	'config_dirs' => array(
		APP_ROOT_DIR.'/_config/',
		APP_ROOT_DIR.'/framework/_config/',
	),

));

?>