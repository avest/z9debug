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

	// password protect production site?
	'password_protect_prod' => false,

	// list dev domains here
	// put shared dev first for deployment
	// there can only be one "shared dev"
	'dev_domains' => array(
	),

	// list staging domains here
	// put main staging site first for deployment
	'stag_domains' => array(
	),

	// list production domains here
	// put main production site first for deployment
	'prod_domains' => array(
	),

	// list root dirs for each domain here
	// convert \ to /
	// "domain name" => "/inetpub/siteXXX/www",
	'root_dirs' => array(
	),

	// list ssl settings for each domain here
	// "domain name" => true/false,
	'has_ssl_list' => array(
	),

));

?>