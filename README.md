Z9 Debug
=========

[Requirements](https://github.com/avest/z9debug#requirements) |
[Installing](https://github.com/avest/z9debug#installing) |
[Getting started](https://github.com/avest/z9debug#getting-started) |
[History](https://github.com/avest/z9debug#history) |
[TODO](https://github.com/avest/z9debug#todo) |
[License](https://github.com/avest/z9debug#license) |
[Credits](https://github.com/avest/z9debug#credits)

Z9 Debug is a stand alone PHP debugging class with it's own separate browser console page for viewing results.

```php
<?php
$a = 1 + 1;
debug::variable($a);
?>
```

Debugging can be turned on for one or more PHP files, functions, or class methods.

Turn debugging on (in the code or by using the console interface). Execute your web page. Click on 
the optional console page link in the top right of your rendered web page. See the debug results.

![Console page shortcut link](https://github.com/avest/z9debug/raw/master/doc/shortcut_link.png)

![Console page example](https://github.com/avest/z9debug/raw/master/doc/console.png)

Z9 Debug is NOT an "interactiv debugger". It captures all the debug data at the time of code execution 
and allows you to efficiently view the data you wanted to see immediately afterwards.

Debug results are organized into sessions and requests using cookies. A logged in developer on a 
single browser is a single session. By default, each developer sees there own session. Within a 
session, there can then be multiple requests by that developer. Each request has it own results. 
Old sessions are automatically purged. 

* Completely stand alone. Can be installed into any site. No database required.

* Can capture variables, user defined strings, stack trace, memory usage, and perform timing analysis.

* Allows for quick visual confirmation of variable data format.

* Quickly confirm code/logic execution during development. 

* For testing, provides a safer alternative to the exit() command that adds reporting of what file 
and line of code the exit occurred.

* Can be used to test/debug production sites without making any code changes.

* Allows for debugging additional AJAX and API calls that make up a web page.

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


## Requirements

- PHP 5.6.0 or newer
- HTTPS enabled site / SSL certificate

## Installing

Download source code:  
[Download Zip File](https://github.com/avest/z9debug/archive/master.zip)  
[Download Tar File](https://github.com/avest/z9debug/archive/master.tar.gz)

Create a folder in your site for the code. To make debug more secure, you might consider obfuscating 
the folder name with something less obvious.
```
$ cd <path to your site>
$ wget https://github.com/avest/z9debug/archive/master.tar.gz
$ tar xvfz master.tar.gz 
$ mv z9debug-master debug
$ rm master.tar.gz
$ cd debug
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

Started in 2005, the class started out as a better way to display print_r() results and 
has slowly evolved one small tweak at a time to make it better as needed.

First rule is that the debug software will always be stand alone so that it can be easily
added to any web site and it will only require one line of code to add it into a site.

The software has evolved since the start, but it has only been enhanced as a direct 
need to further improve productivity during the course of actual real world software projects. 

The first problem was to eliminate the display of output in the middle of rendered HTML. 
The solution for formatted display was to record the data during execution and then properly 
display the data in a separate console window.

To organize the data, the concept of sessions and requests was introduced and tracked with cookies.

Login authentication was added to secure the console page.

Turns out there are a handful of challenges to avoid using large 
amounts of memory during code execution, slowing the page request down, or causing the browser to 
hang when displaying large amounts of data... The goal for additional memory is 16MB.

PHP does not support passing a variable and then knowing what the name of the variable is... so for many 
years, all calls where made like this to pass the variable name: 
```
# use to have to pass the variable name...
debug::variable($my_var, 'my_var'); 
```
The solution to this was to save a copy of the code file that has the debug statement and to
then parse that line of code at the time of rendering the console page.

Another milestone was the ability to debug a site without having to make a code change.
The solution was addressed by the "on/off" interface created within the console. This
feature was made possible by using the PHP-Parse library. (more info below) 

This software was first made publicly open source on GitHub in 2018.

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

