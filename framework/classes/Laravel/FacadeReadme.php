<?php

// NOTE:
// Facade calls are approximately 2.5 times slower than static calls.
// Facade calls are approximately 3.8 times slower than traditional class method calls.

//------------------------------------------------------
// Create classes/Facade/Str.php
//------------------------------------------------------


namespace Facade;

use Laravel\Facade;

class String extends Facade
{
	protected static function getFacadeAccessor() { return 'String'; }
}


//------------------------------------------------------
// Create classes/Z9digital/Library/StringUtil.php
//------------------------------------------------------

namespace Z9digital\Library;

use debug;

class StringUtil
{
	public function __construct()
	{
	}

	public function myfunction()
	{
	}
}

//------------------------------------------------------
// Update /_library.php
//------------------------------------------------------

$di->set('String', function () use ($di) {
	return new \Z9digital\Library\StringUtil();
});

$di->set('Facade', function () use ($di) {
	return new \Laravel\Facade(
		$di
	);
});
$di->get('Facade');


//------------------------------------------------------
// test that the facade works
//------------------------------------------------------
Use Facade\Str;
$test = Str::right('abc', 1);
debug::variable($test, 'test');


// SPEED TEST

// 9.2 seconds for facade
//use Z9digital\Library\String;
//$test = 'abc';
//$timer_id = debug::timer_start('string');
//for ($i=1; $i<=1000000; $i++)
//{
//	Str::right($test, 1);
//}
//debug::timer_end($timer_id);

// 3.7 seconds for static class method calls
//require_once(APP_ROOT_DIR.'/classes/Z9digital/Library/StringStatic.php');
//use Z9digital\Library\StringStatic;
//$test = 'abc';
//$timer_id = debug::timer_start('string');
//for ($i=1; $i<=1000000; $i++)
//{
//	StringStatic::right($test, 1);
//}
//debug::timer_end($timer_id);

// 2.4 seconds for traditional class method calls
//use Z9digital\Library\StringLib;
//$str = new StringLib();
//$test = 'abc';
//$timer_id = debug::timer_start('string');
//for ($i=1; $i<=1000000; $i++)
//{
//	$str->right($test, 1);
//}
//debug::timer_end($timer_id);

// 1.8 seconds for function call
//$test = 'abc';
//$timer_id = debug::timer_start('string');
//for ($i=1; $i<=1000000; $i++)
//{
//	right($test, 1);
//}
//debug::timer_end($timer_id);


?>