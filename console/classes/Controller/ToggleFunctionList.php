<?php
//===================================================================
// z9Debug
//===================================================================
// ToggleFunctionList.php
// --------------------
//
//       Date Created: 2018-10-22
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

namespace Z9\Debug\Console\Controller;

use debug;
use Facade\Dependency;
use Mlaphp\Request;
use Mlaphp\Response;
use Facade\Str;
use Z9\Debug\Console\Authenticate;
use Facade\Action;
use Facade\Config;
use Facade\Php;
use Facade\File;
use Facade\Date;

use PhpParser\Error;
use PhpParser\ParserFactory;


class ToggleFunctionList
{
	/** @var Request */
	protected $request;
	/** @var Response */
	protected $response;
	/** @var \Z9\Debug\Console\Authenticate */
	protected $authenticate;

	public function __construct(
	)
	{
		$this->request = Dependency::inject('Request');
		$this->response = Dependency::inject('Response');
		$this->authenticate = Dependency::inject('\Z9\Debug\Console\Authenticate');
	}

	public function __invoke()
	{
		debug::on(false);
		debug::string('__invoke');

		$is_authenticated = $this->authenticate->is_valid_auth_token();
		debug::variable($is_authenticated);

		if (!$is_authenticated)
		{
			exit();
		}

		$url_parts = parse_url($this->request->_SERVER['REQUEST_URI']);
		debug::variable($url_parts, 'url_parts');

		$base_name = basename($url_parts['path']);
		debug::variable($base_name, 'base_name');

		switch ($base_name)
		{
			case 'toggle_function_list.php':
				$physical_path = $_SERVER['DOCUMENT_ROOT'];
				if (isset($_POST['physical_path']))
				{
					$physical_path = $_POST['physical_path'];
				}
				debug::variable($physical_path);

				return $this->display_toggle_function_list($physical_path);

				break;
		}
	}

	public function display_toggle_function_list($physical_path)
	{
		debug::on(false);
		debug::variable($physical_path);

		if (Str::ends_with($physical_path, DIRECTORY_SEPARATOR))
		{
			$confirm_physical_path = realpath($physical_path).DIRECTORY_SEPARATOR;
		}
		else
		{
			$confirm_physical_path = realpath($physical_path);
		}
		debug::variable($confirm_physical_path);

		if ($physical_path <> $confirm_physical_path)
		{
			exit();
		}

		$is_valid_physical_path = false;
		if (Str::starts_with($physical_path, $_SERVER['DOCUMENT_ROOT']))
		{
			$is_valid_physical_path = true;
		}
		debug::variable($is_valid_physical_path);

		if (!$is_valid_physical_path)
		{
			exit();
		}

		$display_file_path = Str::remove_leading($physical_path, $_SERVER['DOCUMENT_ROOT']);
		debug::variable($display_file_path);

		if (!empty($physical_path))
		{
			$functions = $this->parse_php_file($physical_path);
			debug::variable($functions);
		}

		$is_physical_file = false;
		if (!Str::ends_with($physical_path, DIRECTORY_SEPARATOR))
		{
			$is_physical_file = true;
		}
		debug::variable($is_physical_file);

		if (empty($functions) && $is_physical_file)
		{
			$functions[] = array(
				'file_path' => $physical_path,
				'namespace' => '',
				'class' => '',
				'function' => '',
				'line_number' => '',
			);
			debug::variable($functions);
		}
		else
		{
			if (is_array($functions) && count($functions) > 0 && $is_physical_file)
			{
				$functions = array_merge(
					array(array(
						'file_path' => $physical_path,
						'namespace' => '',
						'class' => '',
						'function' => '',
						'line_number' => '',
					)),
					$functions
				);
				debug::variable($functions);
			}
		}


		// get list of files already set to on
		$force_on = debug::get('force_on');
		debug::variable($force_on);

		$force_on_path = Str::remove_leading($physical_path, $_SERVER['DOCUMENT_ROOT']);
		debug::variable($force_on_path);

		// we only need the settings for the one file
		$on_file_functions = array();
		if (isset($force_on[$force_on_path]))
		{
			$on_file_functions = $force_on[$force_on_path];
		}
		debug::variable($on_file_functions);

		if (is_array($functions))
		{
			foreach ($functions as $key => $function)
			{
				debug::variable($function);

				// Channel/Bigcommerce/Controller/Index::__invoke
				// namespace = Channel\Bigcommerce\Controller
				// class = Index
				$on_file_function_value = Action::_('Z9\Debug\Console\ToggleSettings')->get_on_file_function_value($function['namespace'], $function['class'], $function['function']);
				debug::variable($on_file_function_value);

				$is_on = false;
				if (in_array($on_file_function_value, $on_file_functions))
				{
					$is_on = true;
				}
				debug::variable($is_on);

				$functions[$key]['is_on'] = $is_on;
			}
		}
		debug::variable($functions);

		$this->response->setVars(array(
			'functions' => $functions,
			'physical_path' => $physical_path,
		));
		$this->response->setView('toggle_function_list.tpl.php', 'Z9\Debug\Console');

		return $this->response;

	}


	private function parse_php_file($file_path)
	{
		debug::on(false);
		debug::variable($file_path);

		$start_time = Date::micro_time();

		$code = '';
		if (is_file($file_path))
		{
			$code = File::read_file($file_path);
		}
		//debug::variable($code);

		$code_len = strlen($code);
		debug::variable($code_len);

		// old versions of PHP don't allow this line
		//$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
		$parser_factory = new ParserFactory;
		$parser = $parser_factory->create(ParserFactory::PREFER_PHP7);

		try
		{
			$ast = $parser->parse($code);
			//debug::variable($ast);

			$ast_count = count($ast);
			debug::variable($ast_count);
		}
		catch (Error $error)
		{
			echo "Parse error: {$error->getMessage()}\n";
			return;
		}

		$result = array();

		if (is_array($ast))
		{
			foreach ($ast as $key => $value)
			{
				debug::variable($key);

				$class = get_class($value);
				debug::variable($class);

				// TOP LEVEL FUNCTIONS
				if ($class == 'PhpParser\Node\Stmt\Function_')
				{
					//debug::variable($value);

					$function_name = $value->name;
					//debug::variable($function_name);

					$function_line = $value->getLine();
					//debug::variable($function_line);

					//$result[$file_path]['-']['-'][$function_name] = $function_line;
					$result[] = array(
						'file_path' => $file_path,
						'namespace' => '',
						'class' => '',
						'function' => $function_name,
						'line_number' => $function_line,
					);
				}

				// TOP LEVEL NAMESPACE
				if ($class == 'PhpParser\Node\Stmt\Namespace_')
				{
					//debug::variable($value);

					$namespace_name = $value->name->toString();
					debug::variable($namespace_name);

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

											//$result[$file_path][$namespace_name][$class_name][$class_method] = $class_method_line;
											$result[] = array(
												'file_path' => $file_path,
												'namespace' => $namespace_name,
												'class' => $class_name,
												'function' => $class_method,
												'line_number' => $class_method_line,
											);

										}
									}

								}


							}
						}
					}

				}


				// TOP LEVEL CLASS
				if ($class == 'PhpParser\Node\Stmt\Class_')
				{
					//debug::variable($value);

					$namespace_name = '';
					debug::variable($namespace_name);

					//debug::variable($value);

					$class_name = $value->name;
					//debug::variable($class_name);

					if (is_array($value->stmts))
					{
						foreach ($value->stmts as $value2)
						{
							$class2 = get_class($value2);
							//debug::variable($class2);

							if ($class2 == 'PhpParser\Node\Stmt\ClassMethod')
							{
								//debug::variable($value2);

								$class_method = $value2->name;
								//debug::variable($class_method);

								$class_method_line = $value2->getLine();
								//debug::variable($class_method_line);

								//$result[$file_path][$namespace_name][$class_name][$class_method] = $class_method_line;
								$result[] = array(
									'file_path' => $file_path,
									'namespace' => $namespace_name,
									'class' => $class_name,
									'function' => $class_method,
									'line_number' => $class_method_line,
								);

							}
						}
					}

				}


			}
		}


		$end_time = Date::micro_time();

		$total_time = Date::micro_time_diff($start_time, $end_time);
		debug::variable($total_time);

		debug::variable($result);

		return $result;
	}



}



?>