<?php
$this->setRoutes(array(
	APP_WEB_DIR.'/console/' => 'Z9\Debug\Console\Controller\Index',
	APP_WEB_DIR.'/console/cms.php' => 'Z9\Debug\Console\Controller\Cms',
	APP_WEB_DIR.'/console/error.php' => 'Z9\Debug\Console\Controller\Error',
	APP_WEB_DIR.'/console/file.php' => 'Z9\Debug\Console\Controller\File',
	APP_WEB_DIR.'/console/global.php' => 'Z9\Debug\Console\Controller\Globals',
	APP_WEB_DIR.'/console/index.php' => 'Z9\Debug\Console\Controller\Index',
	APP_WEB_DIR.'/console/menu_settings.php' => 'Z9\Debug\Console\Controller\MenuSettings',
	APP_WEB_DIR.'/console/more.php' => 'Z9\Debug\Console\Controller\More',
	APP_WEB_DIR.'/console/on_off_count.php' => 'Z9\Debug\Console\Controller\OnOffCount',
	APP_WEB_DIR.'/console/page.php' => 'Z9\Debug\Console\Controller\Page',
	APP_WEB_DIR.'/console/request.php' => 'Z9\Debug\Console\Controller\Requests',
	APP_WEB_DIR.'/console/session.php' => 'Z9\Debug\Console\Controller\Session',
	APP_WEB_DIR.'/console/settings.php' => 'Z9\Debug\Console\Controller\Settings',
	APP_WEB_DIR.'/console/sql.php' => 'Z9\Debug\Console\Controller\Sql',
	APP_WEB_DIR.'/console/toggle.php' => 'Z9\Debug\Console\Controller\Toggle',
	APP_WEB_DIR.'/console/toggle_breadcrumb.php' => 'Z9\Debug\Console\Controller\ToggleBreadcrumb',
	APP_WEB_DIR.'/console/toggle_dir_list.php' => 'Z9\Debug\Console\Controller\ToggleDirList',
	APP_WEB_DIR.'/console/toggle_file_list.php' => 'Z9\Debug\Console\Controller\ToggleFileList',
	APP_WEB_DIR.'/console/toggle_function_list.php' => 'Z9\Debug\Console\Controller\ToggleFunctionList',
	APP_WEB_DIR.'/console/toggle_on_list.php' => 'Z9\Debug\Console\Controller\ToggleOnList',
));
?>