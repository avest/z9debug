Z9 Debug
=========

[Requirements](https://github.com/avest/z9debug#requirements) |
[Installing](https://github.com/avest/z9debug#installing) |
[Config Settings](https://github.com/avest/z9debug#config-settings) |
[Security](https://github.com/avest/z9debug#security) |
[Getting Started](https://github.com/avest/z9debug#getting-started) |
[History](https://github.com/avest/z9debug#history) |
[TODO](https://github.com/avest/z9debug#todo) |
[License](https://github.com/avest/z9debug#license) |
[Credits](https://github.com/avest/z9debug#credits)

Z9 Debug is a stand alone PHP debugging class that also includes it's own separate browser console 
for viewing results.

(Please note that this software was written and battle tested over the course of 15+ years. 
I only recently made the software public. The software is stable. 
All of the performance kinks have been worked out.
My team uses the software
on a daily basis within our own projects. It does what we need it to do. I actively 
support the software as needed.)

You can think of Z9 Debug as a replacement for print_r() except with a whole bunch of additional features 
added. 
Most importantly, by having a separate console for viewing results, 
your rendered html pages are untouched. 

It works like this... 1) Turn debugging on (in the code or by using the console interface). 2) Execute your web page. 3) Click on 
the optional console page link in the top right of your rendered web page. 4) See the debug results.

Debugging can be turned on or off for any given file, function, or class method.


Here is some sample code... We are going to debug the code of the first_char() function found in /test.php:  
```php
<?php
include_once($_SERVER['DOCUMENT_ROOT'].'/debug/load.php');

$my_string = 'abcdefg';

$my_char = first_char($my_string);

function first_char($input_string)
{
	debug::on(true);
	debug::string('first_char()');
	debug::variable($input_string);
	
	$return = substr($input_string, 0, 1);
	debug::variable($return);
	
	return $return;
}
?>
```

Console page shortcut link (https://&lt;domain name&gt;/&lt;debug folder&gt;/console/):  
![Console page shortcut link](https://github.com/avest/z9debug/raw/master/doc/shortcut_link.png)

Example console page:
![Console page example](https://github.com/avest/z9debug/raw/master/doc/console.png)

Z9 Debug is NOT an "interactiv debugger". Instead, it captures all the debug data at the time of code execution 
and allows you to view the debug results of the files, methods, and functions that you selected immediately afterwards.

Debug results are organized into "sessions" and "requests". Cookies are used to persist the "session" across requests.
One developer using one site on one browser makes one unique session. By default, each developer sees there own session data. Within a 
session, there can then be multiple requests. Each request has it own results. 
Old sessions are automatically purged. 

## Features

* Completely stand alone. Can be installed into any site. No database required.

* Can capture variables, user defined strings, stack trace, memory usage, and perform timing analysis.

* Allows for quick visual confirmation of variable data.

* Quickly confirm code and business logic execution during development. 

* For testing, provides a safer alternative to the exit() command by reporting what file 
and line of code executed the exit. See debug::str_exit().

* The console allows for turning debug on or off on production sites without making any code changes.

* Easily debug AJAX and API calls as well.

* Does not alter rendered HTML or Javascript of a web page.

* Can be configured to record all SQL statements, the time it takes for each SQL query, and identify 
any slow queries.

* Records date/time, URL, load time, peak memory usage, and total sql query time of a page request.

* Displays a list of all files loaded to process the page request. Optionally, the file list can be 
categorized. (ie: controller, action, gateway, block, view, bootstrap, debug, etc)

* Automatically captures global variables. (ie: $_GET, $_POST, $_FILES, $_COOKIE, $_SERVER, 
$_ENV, $_SESSION). Can be configured to capture CMS variables/objects as well. (ie: User object, 
Page object, etc)

* The login supports a simple password or using a remote API call for authentication. The remote API 
support allows for managing multiple developers across multiple sites easier.

* For data capture performance... the number of array elements captured are 
automatically limited and the number of times the same debug statement is executed inside a loop 
is limited. To minimize peak memory, debug data is periodically saved to a file as needed. 

* For display performance... the console "pages" the results and large arrays are displayed with a 
"more" link.

## Recommendations

You can fairly quickly install Z9 Debug into any code base. It only requires a single include
statement to enable it.

After that, you can quickly add a few debug statements to your code and get instant results.

And then what you can do is over time, add debug statements with any new code that you write or
any existing code that you need to analyze. 

For my team, we have had the best results as far as maximizing productivity, 
minimizing defects, and being able to debug production sites (without any code changes) 
by adding a debug::variable() statement after pretty much every assignment statement that
gets executed in the code. 

The real payoff of Z9 Debug is fully incorporating into your code. 
(We leave the debug statements in the code. Z9 Debug has been designed to keep the added code 
as clean and tidy as possible.)

All of that said, Z9 Debug is a key part of how we code our projects. It is not meant to replace 
testing. It is one more tool that makes coding easier. Assuming you aren't 
starting over from scratch every day, all software ultimately becomes "opinionated software".
The decisions that we make every day as to how we are going to collectively write our software 
as a team results in "opinonated software". Leaving debug statements in your code is an "opinionated" decision. 
You have to decide for yourself and your team if the cost of adding a debug statement
to your code is worth the benefits. 

Our experience is that given the choice between code without debug logic and 
code with, the code with is the much better investment and will be much easier to maintain over time. 
Having the ability to instantly see and 
visually confirm 
the result of every assignment statement, the format of the data in every variable, and the path 
taken by every conditional statement is a huge productivity gain. It saves a ton of time when you
are looking at someone elses code fresh for the first time.
It elimiminates all of the mental processing that would otherwise be required to hold a picture in 
your head of what the code is doing and what the data looks like. It is easier to visually 
confirm results. It is easier to write a few lines of code and quickly confirm your code as you write it. 

My recommendation is that you spend a few minutes to install the software. Try 
adding a few debug statements to your code.
Spend some time adding debugging statements to every assignment statement of a given method or function
to get a feel for how it can work.
Commit to using it rigorously for a few weeks to give it a chance.
The real payoff comes over time when you find that you are able to debug and analyze code without adding 
any new code. That is powerful. 


## Requirements

- PHP 5.6.0 or newer
- HTTPS enabled site / SSL certificate

## Installing

Download source code:  
[Download Zip File](https://github.com/avest/z9debug/archive/master.zip)  
[Download Tar File](https://github.com/avest/z9debug/archive/master.tar.gz)
```
$ cd <path to your site>
$ wget https://github.com/avest/z9debug/archive/master.tar.gz
$ tar xvfz master.tar.gz 
```

To make debug more secure, you might consider obfuscating 
the folder name with something less obvious than "debug".
```
$ mv z9debug-master debug
$ rm master.tar.gz
```

Make the sessions folder writeable:
```
$ cd debug
$ chmod 775 sessions
```

Setup [Composer](http://getcomposer.org) (if needed)  

Then run (using the included composer.json file)
```
$ php composer.phar update
```
Or
```
$ composer update
```
Or (ignore the composer.json file)
```
$ composer require nikic/php-parser:3.1
```

## Config Settings

Copy config_settings.sample.php to config_settings.php:  
```
$ cd settings
$ cp config_settings.sample.php config_settings.php
```

Edit config_settings.php:  
```php
<?php

// OPTIONAL
// Remote authentication is ideal for when you want to use debug on
// multiple sites for the same set of developers.
// Specify the URL of a remote authentication API.
// For security reasons, make sure you use HTTPS.
// eg: https://<your domain>/<your_authentication_page>
// Z9 Debug will pass the username and password values from the login screen as POST variables to
// the URL specified.
// $_POST['developer_user']
// $_POST['developer_password']
// If the API request then returns a "1" value, the user will be authenticated.
debug::set('remote_authentication', '');

// If remote authentication is blank, then a single password can be used for authentication.
debug::set('password', '');

// REQUIRED
// A secret is used to encrypt the authentication token value saved to a cookie.
// Enter a random 8+ character value.
debug::set('secret', '');

// OPTIONAL
// If you want to populate user and page data to the "CMS" page in the console,
// set is_cms_installed to true and then call debug::set_cms_user() and/or
// debug::set_cms_page() to populate the data.
debug::set('is_cms_installed', false);

// It is recommended that force_http always be set to false.
// But if you truly need to acces the debug console and don't have HTTPS enabled, you
// can set force_http to true to bypass the HTTPS security check.
// Better yet, see https://letsencrypt.org to install a free SSL certificate on all of your development sites.
debug::set('force_http', false);

?>
```

Optional, setup file_categories.php:  
```
$ cd settings
$ cp file_categories.sample.php file_categories.php
```

Optional, edit file_categories.php:  
```php
<?php

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
```

## Security

Set the "password" or "remote_authentication" setting in the config_settings.php file. 

Assign a unique value to the "secret" setting in the config_settings.php file.

Use HTTPS / SSL Certificate on the site that has debug.

If using a remote authentication API call, use HTTPS / SSL certificate on the API site.

For free SSL Certificates, check out [LetsEncrypt](https://letsencrypt.org).

Use strong passwords.

Consider obfuscating the folder name that has the debug software to make it hard to guess/detect.

Be sure to prevent files located in the "sessions" folder from being viewable in a browser.




## Getting started

**ENABLED** 
```php
debug::enabled(false); // Disable debug entirely
// Typically used to disable debug on production site if needed.
```
  
**ON**
```php
debug::on(); // Turn debug on
debug::on(false); // Don't turn debug on
debug::on(true); // Turn debug on
// "On" is limited to a single file, function or class method.
// Use debug::on(); when you want the code to always generate debug.
// Use debug::on(true); and debug::on(false); to toggle debug on and off.
// Search for debug::on(true) and replace with debug::on(false) prior to committing code.
// Note: debug must be enabled in order for debug::on() to work.
```  
  
**OFF**
```php
debug::off(); // Turn debug off
debug::off(true); // Turn debug off
debug::off(false); // Don't turn debug off
// "Off" is limited to a single file, function or class method.
```
  
**IS_ON** and **IS_OFF**
```php
if (debug::is_on()) { ... }
if (debug::is_off()) { ... }
```
  
**VARIABLE**
```php
debug::variable($my_var);
debug::variable($array[$index], 'array['.$index.']'); // second parameter is variable name
debug::variable($my_var, null, array('limit'=>1)); // add loop count limit
debug::variable($my_var, null, array('start'=>10, 'limit'=>1)); // add loop start and count limit
debug::variable($my_var, null, array('all_lines'=>true)); // show all lines for a variable, override the 8000 line limit
```
  
**STRING**
```php
debug::string('string');
// debug::timestamp('string'); // add [yyyy-mm-dd hh:mm:ss] timestamp in front of string
// debug::micro_timestamp('string'); // add [yyyy-mm-dd hh:mm:ss.ssssssss] timestamp in front of string
```
  
**STR_EXIT**
```php
debug::str_exit('string'); // 'string' is optional
// exit() is only executed if debug is on.
// Use this instead of exit(); so that you never have to figure out where the code halted.
```
  
**STACK_TRACE**
```php
debug::stack_trace();
```
  
**MEMORY**
```php
debug::memory();
```
  
**TIMING**
```php
$timer_id = debug::timer_start('timer_name');
// put code here...
$time = debug::timer_end($timer_id);
```
  
**SQL RECORDING**
```php
$sql_timer_id = debug::sql_start($sql, debug_backtrace());
// put sql query here...
$time = debug::sql_end($sql_timer_id);
```
  
**DISABLE SQL RECORDING**
```php
debug::set('disable_sql_recording', true);
// For performance and memory reasons, use this to prevent all sql recording.
// This is typically set for data processing jobs.
```
  
**SESSION_NAME**
```php
debug::session_name('put name here');
// Use this to give a session a name which you can then find in the console.
```
  
**SUPPRESS_OUTPUT**
```php
debug::suppress_output(true); // don't show the console link, default is true if not set
// This is required for ajax requests and generating excel files.
// The console link is https://<domain name>/<debug folder>/console/
```
  
**DEFAULT_LIMIT**
```php
debug::set('default_limit', 50); // override default loop count "limit" of 10
```
  
**GET**
```php
debug::get('suppress_output');
debug::get('enabled');
debug::get('disable_sql_recording');
debug::get('session_name');
debug::get('default_limit');
```
  
**LEGACY**
```php
debug::print_var($var, 'var_name');
// Use this to echo variable to output instead of console.
```

## History

Z9 Debug was developed over 14 years, starting in 2005, and then open sourced in 2018.

The debug class started out as a better way to display print_r() results and has slowly 
evolved as needed within scope of building real world projects one small enhancement at a time.

The original inspiration was to separate the display of debug results from the actual
rendered HTML of a web site.
 
The obvious but maybe not easy solution was to capture debug results during code execution and create a separate console window
for the display of the results.

One of the business rules that we have stuck to is that the debug tool is completely "stand alone" and that 
it can be added to any web site project with one line of code.

To organize the data, the concept of sessions and requests was introduced and tracked with cookies. Login 
authentication was added to secure the console.

Performance has always been a primary issue during development. Specifically, memory usage, keeping the page 
requests fast, and not allowing the console page to blow up or hang the browser with too much content.  
Worse case, the code is designed to use a maximum of approximately 16MB of additional memory wheh debug is enabled.

PHP does not support passing a variable and then knowing what the name of the variable is... so for many 
years, all calls where made like this: 
```
# use to have to pass the variable name...
debug::variable($my_var, 'my_var'); 
```
Retyping the name of a variable was always so clunky. The solution was to save a copy of the PHP code 
file that has the debug statement and to then parse that line of code at the time of rendering the 
console page. The debug::variable() statement is much cleaner now.

Another hurdle was how to debug a site without having to make code changes.
The solution was addressed by the "on/off" interface created within the console. Credit: this
feature was made possible by using the PHP-Parse library. (more info below) 


## TODO

In general, new features and bug fixes are periodically added as needed to further improve 
development productivity.

## Contributing

Per the terms of the license, you are free to use, modify, and fork the software as needed for 
your own use. If you would like to contribute a change/modification, please contact me first.
Please note that my availability is most often limited.

## License

The license is based on the [BSD license](https://opensource.org/licenses/BSD-3-Clause) with
a modification to address use of the "Z9 Debug" name.

## Credits

The "on/off" feature in the console was made possible by PHP-Parser found here:
https://github.com/nikic/PHP-Parser

Between 2005 and 2018, the original source code was created by Allan Vest, Z9 Digital
and was used in Z9 Digital software projects during that time.

