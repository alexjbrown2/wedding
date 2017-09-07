<?php

	// Include the credentials

	include('../.secrets.php');

	// Server path constatns
	
	define('PATH_ROOT_DIR',                      '/var/www/html');						// EDIT //
	define('PATH_SERVER',                        'http://' . @$_SERVER['HTTP_HOST']);			// EDIT //		
	define('PATH_SEC_SERVER',                    'https://' . @$_SERVER['HTTP_HOST']);			// EDIT //		
	define('PATH_WWW',                           'wedding');							// EDIT //
	
	// Error hanlding
	
	define('ERRORS_SQL',                        1);		// 1 = DB errors on screen, 2 = Generic DB errors on screen (Actual DB errors logged to file)
	define('DEFAULT_LOG_RETENTION',             60 * 60 * 24 * 7);		// Retain logs for 7 days
	define('DEFAULT_ERROR_LOG_FILE',            PATH_LOGS . '/error.log');
		
	// Database info
	
	define('DB_HOST',                           'localhost');
	define('DB_BASE',                           'wedding');
	define('DB_USER',                           $GLOBAL_DB_USERNAME);
	define('DB_PASS',                           $GLOBAL_DB_PASSWORD);
	
?>
