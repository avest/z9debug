<?php
//===================================================================
// z9Debug
//===================================================================
// test_php_parser.php
// --------------------
// testing php parser library
//
//       Date Created: 2018-03-17
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

define('Z9DEBUG_CONSOLE', true);

define('Z9DEBUG_DIR', dirname(dirname( __FILE__ )));

include(Z9DEBUG_DIR.'/load_console.php');
debug::on(false);

include(Z9DEBUG_DIR.'/vendor/autoload.php');
include(Z9DEBUG_DIR.'/console/functions/console.php');

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;

use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;

//$code = <<<'CODE'
//<?php
//
//function test($foo)
//{
//    var_dump($foo);
//}
//CODE;

$file_name = $_SERVER['DOCUMENT_ROOT'].'/cms/classes/Menu.php';
//$file_name = $_SERVER['DOCUMENT_ROOT'].'/_debug/console/functions/console.php';
debug::variable($file_name);

$start_time = get_micro_time();

$code = read_file($file_name);


$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
try
{
	$ast = $parser->parse($code);
	//debug::variable($ast);
}
catch (Error $error)
{
	echo "Parse error: {$error->getMessage()}\n";
	return;
}

//$dumper = new NodeDumper;
//echo '<pre>'.$dumper->dump($ast) . "</pre>\n";


$result = array();


if (is_array($ast))
{
	foreach ($ast as $key => $value)
	{
		//debug::variable($key);

		$class = get_class($value);
		//debug::variable($class);

		// TOP LEVEL FUNCTIONS
		if ($class == 'PhpParser\Node\Stmt\Function_')
		{
			//debug::variable($value);

			$function_name = $value->name;
			//debug::variable($function_name);

			$function_line = $value->getLine();
			//debug::variable($function_line);

			$result[$file_name]['-']['-'][$function_name] = $function_line;
		}

		// TOP LEVEL NAMESPACE
		if ($class == 'PhpParser\Node\Stmt\Namespace_')
		{
			//debug::variable($value);

			$namespace_name = $value->name->toString();
			//debug::variable($namespace_name);

			if (is_array($value->stmts))
			{
				foreach ($value->stmts as $value2)
				{
					$class2 = get_class($value2);
					//debug::variable($class2);

					// TOP LEVEL CLASSES WITHIN NAMESPACE
					if ($class2 == 'PhpParser\Node\Stmt\Class_')
					{
						//debug::variable($value2);

						$class_name = $value2->name;
						//debug::variable($class_name);

						if (is_array($value2->stmts))
						{
							foreach ($value2->stmts as $value3)
							{
								$class3 = get_class($value3);
								//debug::variable($class3);

								if ($class3 == 'PhpParser\Node\Stmt\ClassMethod')
								{
									//debug::variable($value3);

									$class_method = $value3->name;
									//debug::variable($class_method);

									$class_method_line = $value3->getLine();
									//debug::variable($class_method_line);

									$result[$file_name][$namespace_name][$class_name][$class_method] = $class_method_line;

								}
							}

						}


					}
				}
			}

		}

	}
}


$end_time = get_micro_time();

echo display_micro_time_diff($start_time, $end_time);

debug::variable($result);

?>