<?php
//===================================================================
// z9Debug
//===================================================================
// file_categories.sample.php
// --------------------
// Sample file categories settings
//
//       Date Created: 2018-03-05
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

//-----------------------------------------------------------------------------------
// Copy this sample file to /settings/file_categories.php and set the values below
//-----------------------------------------------------------------------------------

// This file is optional.

// The settings in this file, modify the display of the "File" page in the console.

// You would setup this file based on your framework.

// Level 1 of the array is the file type. It can be any value. eg: 'Controller', 'Block', 'View', 'Action', 'Gateway', 'Facade', 'Bootstrap', 'Debug', etc...

// Put the level 1 file types in the order that you want to view them...

// For each file type, you can then specify what files to 'include' and what files to 'exclude' using a regular expression.

// If you create a file_categories file that works for a particular framework, please pass it along and we will add it.

debug::set('file_categories', array(
	'Controller' => array(
		'include' => array(
			'(.*)classes\/Controller(.*)',
		),
		'exclude' => array(
		),
	),
	'Block' => array(
		'include' => array(
			'(.*)_block.php',
		),
		'exclude' => array(
		),
	),
	'View' => array(
		'include' => array(
			'(.*).tpl.php',
		),
		'exclude' => array(
		),
	),
	'Gateway' => array(
		'include' => array(
			'(.*)Gateway.php',
		),
		'exclude' => array(
		),
	),
	'Facade' => array(
		'include' => array(
			'(.*)classes\/Facade(.*)',
		),
		'exclude' => array(
		),
	),
	'Debug' => array(
		'include' => array(
			'(.*)debug(.*)',
		),
		'exclude' => array(
		),
	),
));

?>