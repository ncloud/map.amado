<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* TIME */
define('ONE_HOUR', 60*60*1);
define('ONE_DAY', 60*60*24);
define('SEVEN_DAY', 60*60*24*7);
define('ONE_WEEK', 60*60*24*7);
define('ONE_YEAR', 60*60*24*365);

/* VENDOR */
define('FACEBOOK_VENDOR', 1);
define('TWITTER_VENDOR', 2);

/* DEFAULT */
define('DEFAULT_LAT','37.5935645');
define('DEFAULT_LNG','127.0010451');

define('TITLE', '아마도.지도');

// TYPE_ID
define('IMPORT_TYPE_ID', 50000);
define('NO_TYPE_ID', 50001);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/* End of file constants.php */
/* Location: ./application/config/constants.php */