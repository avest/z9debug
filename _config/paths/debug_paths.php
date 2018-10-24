<?php

use Facade\Config;
use Facade\Str;

$web_root = Str::remove_leading(str_replace("\\", "/", Z9DEBUG_DIR), str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']));
debug::variable($web_root);

$web_root = str_replace("\\", "/", $web_root);
debug::variable($web_root);

$data_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions';
debug::variable($data_dir);

$z9debug_dir = str_replace("\\", "/", Z9DEBUG_DIR);
debug::variable($z9debug_dir);

$document_root = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
debug::variable($document_root);

$sessions_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'sessions';
debug::variable($sessions_dir);

$physical_dir = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR;
debug::variable($physical_dir);

$settings_dir = Z9DEBUG_DIR.DIRECTORY_SEPARATOR.'settings';
debug::variable($settings_dir);

Config::set('path', array(
	'debug' => array(
		'web_root' => $web_root,
		'data_dir' => $data_dir,
		'z9debug_dir' => $z9debug_dir,
		'document_root' => $document_root,
		'sessions_dir' => $sessions_dir,
		'physical_dir' => $physical_dir,
		'settings_dir' => $settings_dir,
	),
));

unset($web_root);
unset($data_dir);
unset($z9debug_dir);
unset($document_root);
unset($sessions_dir);
unset($physical_dir);
unset($settings_dir);

?>