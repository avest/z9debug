<?php
	// NOTE:
	// when the Config class is loaded, it will load these into
	// site.namespace_prefixes

	// PSR-4 autoloader namespace prefixes
	// namespace prefix => base directory
	// http://www.php-fig.org/psr/psr-4/
	$this->namespace_prefixes = array(
		'Z9\Debug\Console' => array(
			'classes_dir' => APP_ROOT_DIR.'/console/classes/',
			'views_dir' => APP_ROOT_DIR.'/console/views/',
		),
		'Facade' => array(
			'classes_dir' => APP_ROOT_DIR.'/framework/classes/Facade/',
		),
		'Z9\Framework' => array(
			'classes_dir' => APP_ROOT_DIR.'/framework/classes/',
			'views_dir' => APP_ROOT_DIR.'/framework/views/',
		),
		'Mlaphp' => array(
			'classes_dir' => APP_ROOT_DIR.'/framework/classes/Mlaphp/',
		),
		'Laravel' => array(
			'classes_dir' => APP_ROOT_DIR.'/framework/classes/Laravel/',
		),
		'*' => array(
			'classes_dir' => APP_ROOT_DIR.'/classes/', // PSR-0 legacy default
			'views_dir' => APP_ROOT_DIR.'/views/',
		),
	);
?>