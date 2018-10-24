<?php

// see
// https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata

namespace PHPSTORM_META { 								// we want to avoid the pollution

	/** @noinspection PhpUnusedLocalVariableInspection */ 		// just to have a green code below
	/** @noinspection PhpIllegalArrayKeyTypeInspection */
	$STATIC_METHOD_TYPES = [								// we make sections for scopes
		\Facade\Action::_('') => [

			'Z9\Debug\Console\CmsData' instanceof \Z9\Debug\Console\Action\CmsDataAction,
			'Z9\Debug\Console\CodeFile' instanceof \Z9\Debug\Console\Action\CodeFileAction,
			'Z9\Debug\Console\FileData' instanceof \Z9\Debug\Console\Action\FileDataAction,
			'Z9\Debug\Console\GlobalData' instanceof \Z9\Debug\Console\Action\GlobalDataAction,
			'Z9\Debug\Console\PageData' instanceof \Z9\Debug\Console\Action\PageDataAction,
			'Z9\Debug\Console\RequestData' instanceof \Z9\Debug\Console\Action\RequestDataAction,
			'Z9\Debug\Console\SessionData' instanceof \Z9\Debug\Console\Action\SessionDataAction,
			'Z9\Debug\Console\SqlData' instanceof \Z9\Debug\Console\Action\SqlDataAction,
			'Z9\Debug\Console\ToggleSettings' instanceof \Z9\Debug\Console\Action\ToggleSettingsAction,
			'Z9\Debug\Console\User' instanceof \Z9\Debug\Console\Action\UserAction,
			'Z9\Debug\Console\VarData' instanceof \Z9\Debug\Console\Action\VarDataAction,

		],

	];
}

?>