<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
define('DEFAULT_CAR_IMG', 'assets/images/mobil-defaultimg.png');


define("MVS_ID", "Ni3meL0WjFWHszwuIwojldsm24foApK8");
define("MVS_SEC", "yTJ5D4GhZGtS2FQEENk35PAwVoupmt37v8G0nU2Z8rA5Fga278FQ375EyNx066sr");
// define("GAPI_KEY", "AIzaSyACUUapMkfSAMwXmdOSgkOteL5kg0oiPRI");
define("GAPI_KEY", "AIzaSyCn3LvelJCQOUQrQ6wMQyOkLohYexTtJjE");
define("GCM_ACT", "https://android.googleapis.com/gcm/send");
define("MVS_AUTH", "https://api.moves-app.com/oauth/v1/");
define("MVS_API", "https://api.moves-app.com/api/1.1");
define("TSTATUS", "https://api.twitter.com/1.1/statuses/user_timeline.json");
define("APNS_CERT", '/var/www/html/application/cert/apns_ak_push.pem');
define("TCUST_KEY", "nU2eyoBRU0U7fQOUXCZZAw");
define("TCUST_SEC_KEY", "cp8NYtnRFNZ9nOcukALLq8Eb3mhvIcg0kjQ65D3F8");
define("TACC_TOKEN", "141346324-w2e7PJYnpeIRXwyYkC1KoQSNcJnuQnzgkaXbsB72");
define("TACC_SEC_TOKEN", "HEwVo1JqJZr75FtNBSaj59GHtTLdaBKoMcfgJE3hnrTYy");
define("IDF", "co.id.jayadata.akexperience");
define("GW", 'ssl://gateway.push.apple.com:2195');




define("IAPI_KEY", "");
define("APN_ACT", "");
// define('SPAREPART_THUMB',		'');


/* End of file constants.php */
/* Location: ./application/config/constants.php */