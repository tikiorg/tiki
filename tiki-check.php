<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
/*
About the design:
tiki-check.php is designed to run in 2 modes
1) Regular mode. From inside Tiki, in Admin | General
2) Stand-alone mode. Used to check a server pre-Tiki installation, by copying (only) tiki-check.php onto the server and pointing your browser to it.
tiki-check.php should not crash but rather avoid running tests which lead to tiki-check crashes.
*/
if (file_exists('./db/local.php') && file_exists('./templates/tiki-check.tpl')) {
	$standalone = false;
	require_once ('tiki-setup.php');
	$access->check_permission('tiki_p_admin');
} else {
	$standalone = true;
	$render = "";

	/**
	 * @param $string
	 * @return mixed
	 */
	function tra($string)
	{
		return $string;
	}

	/**
	  * @param $var
	  */
	function renderTable($var)
	{
		global $render;
		if (is_array($var)) {
			$render .= '<table style="border:2px solid grey;">';
			foreach ($var as $key => $value) {
				$render .= '<tr style="border:1px solid">';
				$render .= '<td style="border:1px black;padding:5px;white-space:nowrap;">';
				$render .= $key;
				$render .= "</td>";
				$iNbCol=0;
				foreach ($var[$key] as $key2 => $value2) {
					$render .= '<td style="border:1px solid;';
					if ($iNbCol != count(array_keys($var[$key]))-1) {
						$render .= 'text-align: center;white-space:nowrap;';
					}
					$render .= '"><span class="';
					switch($value2) {
						case 'good':
						case 'safe':
						case 'ugly':
						case 'bad':
						case 'risky':
						case 'info':
							$render .= "button $value2";
							break;
					}
					$render .= '">'.$value2.'</span></td>';
					$iNbCol++;
				}
				$render .= '</tr>';
			}
			$render .= '</table>';
		} else {
			$render .= 'Nothing to display.';
		}
 	}
}

// Get PHP properties and check them
$php_properties = false;

// Check error reporting level
$e = error_reporting();
$d = ini_get('display_errors');
if ( $e == 0 ) {
	if ($d != 1) {
		$php_properties['Error reporting'] = array(
			'fitness' => tra('info'),
			'setting' => 'Disabled',
			'message' => tra('You will get no errors reported, because error_reporting and display_errors are both turned off. This might be the right thing for a production site, but in case of problems enable these in php.ini to get more information.')
		);
	} else {
		$php_properties['Error reporting'] = array(
			'fitness' => tra('info'),
			'setting' => 'Disabled',
			'message' => tra('You will get no errors reported although display_errors is On, because the error_reporting level is set to 0. This might be the right thing for a production site, but in case of problems raise the value in php.ini to get more information.')
		);
	}
} elseif ( $e > 0 && $e < 32767) {
	if ($d != 1) {
		$php_properties['Error reporting'] = array(
			'fitness' => tra('info'),
			'setting' => 'Disabled',
			'message' => tra('You will get no errors reported, because display_errors is turned off. This might be the right thing for a production site, but in case of problems enable it in php.ini to get more information. Your error_reporting level is decent at '.$e.'.')
		);
	} else {
		$php_properties['Error reporting'] = array(
			'fitness' => tra('info'),
			'setting' => 'Partly',
			'message' => tra('You will not get all errors reported as your error_reporting level is at '.$e.'. '.'This is not necessarily a bad thing (and it might be just right for production sites) as you will still get critical errors reported, but sometimes it can be handy to get more information. Check your error_reporting level in php.ini in case of having issues.')
		);
	}
} else {
	if ( $d != 1 ) {
		$php_properties['Error reporting'] = array(
			'fitness' => tra('info'),
			'setting' => 'Disabled',
			'message' => tra('You will get no errors reported although your error_reporting level is all the way up at '.$e.', but display_errors is off. This might be the right thing for a production site, but in case of problems enable it in php.ini to get more information.')
		);
	} else {
		$php_properties['Error reporting'] = array(
			'fitness' => tra('info'),
			'setting' => 'Full',
			'message' => tra('You will get all errors reported as your error_reporting level is all the way up at '.$e.' and display_errors is on. Way to go in case of problems as the error reports usually contain some valuable hints!')
		);
	}
}

// Now we can raise our error_reporting to make sure we get all errors
// This is especially important as we can't use proper exception handling with PDO as we need to be PHP 4 compatible
error_reporting(-1);

// Check if ini_set works
if (function_exists('ini_set')) {
	$php_properties['ini_set'] = array(
		'fitness' => tra('good'),
		'setting' => 'Enabled',
		'message' => tra('ini_set is used in some places to accomodate for special needs of some Tiki features.')
	);
	// As ini_set is available, use it for PDO error reporting
	ini_set('display_errors', '1');
} else {
	$php_properties['ini_set'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'Disabled',
		'message' => tra('ini_set is used in some places to accomodate for special needs of some Tiki features. Check disable_functions in your php.ini.')
	);
}

// First things first
// If we don't have a DB-connection, some tests don't run
$s = extension_loaded('pdo_mysql');
if ($s) {
	$php_properties['DB Driver'] = array(
		'fitness' => tra('good'),
		'setting' => 'PDO',
		'message' => tra('The PDO extension is the suggested database driver/abstraction layer.')
	);
} elseif ( $s = extension_loaded('mysqli') ) {
	$php_properties['DB Driver'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'MySQLi',
		'message' => tra('You do not have the recommended PDO database driver/abstraction layer. You do have the MySQLi driver though, so we will to fall back to the AdoDB abstraction layer that is bundled with Tiki.')
	);
} elseif ( extension_loaded('mysql') ) {
	$php_properties['DB Driver'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'MySQL',
		'message' => tra('You do not have the recommended PDO database driver/abstraction layer. You do have the MySQL driver though, so we will to fall back to the AdoDB abstraction layer that is bundled with Tiki.')
	);
} else {
	$php_properties['DB Driver'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('You do not have any of the supported database drivers (PDO/mysqli/mysql) loaded. Tiki will not work.')
	);

}

// Now connect to the DB and make all our connectivity methods work the same
$connection = false;
if ( $standalone ) {
	if ( empty($_POST['dbhost']) && !($php_properties['DB Driver']['setting'] == 'Not available') ) {
			$render .= <<<DBC
<h2>Database credentials</h2>
Couldn't connect to database, please provide valid credentials.
<form method="post" action="{$_SERVER['REQUEST_URI']}">
	<p><label for="dbhost">Database host</label>: <input type="text" id="dbhost" name="dbhost" value="localhost" /></p>
	<p><label for="dbuser">Database username</label>: <input type="text" id="dbuser" name="dbuser" /></p>
	<p><label for="dbpass">Database password</label>: <input type="password" id="dbpass" name="dbpass" /></p>
	<p><input type="submit" value=" Connect " /></p>
</form>
DBC;
	} else {
		switch ($php_properties['DB Driver']['setting']) {
			case 'PDO':
				// We don't do exception handling here to be PHP 4 compatible
				$connection = new PDO('mysql:host='.$_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass']);
				/**
				  * @param $query
				   * @param $connection
				   * @return mixed
				  */
				function query($query, $connection)
				{
					$result = $connection->query($query);
					$return = $result->fetchAll();
					return($return);
				}
				break;
			case 'MySQLi':
				$error = false;
				$connection = new mysqli($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass']);
				$error = mysqli_connect_error();
				if ( !empty($error) ) {
					$connection = false;
					$render .= 'Couldn\'t connect to database: '.$error;
				}
				/**
				 * @param $query
				 * @param $connection
				 * @return array
				 */
				function query($query, $connection)
				{
					$result = $connection->query($query);
					$return = array();
					while (	$row = $result->fetch_assoc() ) {
						$return[] = $row;
					}
					return($return);
				}
				break;
			case 'MySQL':
				$connection = mysql_connect($_POST['dbhost'], $_POST['dbuser'], $_POST['dbpass']);
				if ( $connection === false ) {
					$render .= 'Cannot connect to MySQL. Wrong credentials?';
				}
				/**
				 * @param $query
				 * @param string $connection
				 * @return array
				 */
				function query($query, $connection = '')
				{
					$result = mysql_query($query);
					$return = array();
					while (	$row = mysql_fetch_array($result) ) {
						$return[] = $row;
					}
					return($return);
				}
				break;
		}
	}
} else {
	/**
	  * @param $query
	  * @return array
	  */
	function query($query)
	{
		global $tikilib;
		$result = $tikilib->query($query);
		$return = array();
		while ( $row = $result->fetchRow() ) {
			$return[] = $row;
		}
		return($return);
	}
}

// Basic Server environment
$server_information['Operating System'] = array(
	'value' => PHP_OS,
);

if ( PHP_OS == 'Linux' && function_exists('exec') ) {
	exec('lsb_release -d', $output, $retval);
	if ( $retval == 0 ) {
		$server_information['Release'] = array(
			'value' => str_replace('Description:', '', $output[0])
		);
	} else {
		$server_information['Release'] = array(
			'value' => tra('N/A')
		);
	}
}

$server_information['Web Server'] = array(
	'value' => $_SERVER['SERVER_SOFTWARE']
);

$server_information['Server Signature']['value'] = !empty($_SERVER['SERVER_SIGNATURE']) ? $_SERVER['SERVER_SIGNATURE'] : 'off';

// Free disk space
if (function_exists('disk_free_space')) {
	$bytes = disk_free_space('.');
	$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
	$base = 1024;
	$class = min((int) log($bytes, $base), count($si_prefix) - 1);
	$free_space = sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class];
	if ( $bytes < 200 * 1024 * 1024 ) {
		$server_properties['Disk Space'] = array(
			'fitness' => 'bad',
			'value' => $free_space,
			'message' => tra('You have less than 200 megs of free disk space. Tiki will not fit on this disk drive.')
		);
	} elseif ( $bytes < 250 * 1024 * 1024 ) {
		$server_properties['Disk Space'] = array(
			'fitness' => 'ugly',
			'value' => $free_space,
			'message' => tra('You have less than 250 megs of free disk space. This is quite tight. Tiki needs disk space for compiling templates and for uploading files.').' '.tra('When the disk runs full you will not be able to log into your Tiki any more.').' '.tra('We can not reliably check for quotas, so be warned that if your server makes use of them you might have less disk space available.')
		);
	} else {
		$server_properties['Disk Space'] = array(
			'fitness' => 'good',
			'value' => $free_space,
			'message' => tra('You have more than 251 megs of free disk space. Tiki will run nicely, but you may run into issues when your site grows (e.g. file uploads)').' '.tra('When the disk runs full you will not be able to log into your Tiki any more.').' '.tra('We can not reliably check for quotas, so be warned that if your server makes use of them you might have less disk space available.')
		);
	}
} else {
		$server_properties['Disk Space'] = array(
			'fitness' => 'N/A',
			'value' => 'N/A',
			'message' => tra('The PHP function disk_free_space is not available on your server, so we can\'t check for this.')
		);
}

// PHP Version
if (version_compare(PHP_VERSION, '5.1.0', '<')) {
	$php_properties['PHP version'] = array(
		'fitness' => 'bad',
		'setting' => phpversion(),
		'message' => 'You can not run any supported versions of Tiki with this very old version of PHP. Please see http://doc.tiki.org/Requirements for details.'
	);
} elseif (version_compare(PHP_VERSION, '5.2.0', '<')) {
	$php_properties['PHP version'] = array(
		'fitness' => tra('ugly'),
		'setting' => phpversion(),
		'message' => 'You have a quite old version of PHP. You can run Tiki 6.x LTS but not later versions.'
	);
} elseif (version_compare(PHP_VERSION, '5.3.0', '<')) {
	$php_properties['PHP version'] = array(
		'fitness' => tra('ugly'),
		'setting' => phpversion(),
		'message' => 'You have a somewhat old version of PHP. You can run Tiki 6.x LTS or 9.x LTS but not later versions.'
	);
} else {
	$php_properties['PHP version'] = array(
		'fitness' => tra('good'),
		'setting' => phpversion(),
		'message' => 'You have a recent version of PHP and you can run any supported versions of Tiki.'
	);
}

// PHP Server API (SAPI)
$s = php_sapi_name();
if (substr($s, 0, 3) == 'cgi') {
	$php_properties['PHP Server API'] = array(
		'fitness' => tra('info'),
		'setting' => $s,
		'message' => tra('You are running PHP as CGI. Feel free to use a threaded Apache MPM to increase performance.')
	);
} elseif (substr($s, 0, 3) == 'fpm') {
	$php_properties['PHP Server API'] = array(
		'fitness' => tra('info'),
		'setting' => $s,
		'message' => tra('You are running PHP using FPM (Fastcgi Process Manager). Feel free to use a threaded Apache MPM to increase performance.')
	);
} else {
	$php_properties['PHP Server API'] = array(
		'fitness' => tra('info'),
		'setting' => $s,
		'message' => tra('You are not running PHP as CGI. Be aware that PHP is not thread-safe and you should not use a threaded Apache MPM (like worker).')
	);
}

// ByteCode Cache
if ( function_exists('apc_sma_info') && ini_get('apc.enabled') ) {
	$php_properties['ByteCode Cache'] = array(
		'fitness' => tra('good'),
		'setting' => 'APC',
		'message' => tra('You are using APC as your ByteCode Cache which increases performance, if correctly configured. See Admin->Performance in your Tiki for more details.')
	);
} elseif ( function_exists('xcache_info') && ( ini_get('xcache.cacher') == '1' || ini_get('xcache.cacher') == 'On' ) ) {
	$php_properties['ByteCode Cache'] = array(
		'fitness' => tra('good'),
		'setting' => 'xCache',
		'message' => tra('You are using xCache as your ByteCode Cache which increases performance, if correctly configured. See Admin->Performance in your Tiki for more details.')
	);
} elseif ( function_exists('wincache_ocache_fileinfo') && ( ini_get('wincache.ocenabled') == '1') ) {
	$sapi_type = php_sapi_name();
	if ($sapi_type == 'cgi-fcgi') {
		$php_properties['ByteCode Cache'] = array(
			'fitness' => tra('good'),
			'setting' => 'WinCache',
			'message' => tra('You are using WinCache as your ByteCode Cache which increases performance, if correctly configured. See Admin->Performance in your Tiki for more details.')
		);
	} else {
		$php_properties['ByteCode Cache'] = array(
			'fitness' => tra('ugly'),
			'setting' => 'WinCache',
			'message' => tra('You are using WinCache as your ByteCode Cache, but you do not seem to use the required CGI/FastCGI server API.')
		);
	}
} else {
	if(check_isIIS()) {
		$php_properties['ByteCode Cache'] = array(
			'fitness' => tra('info'),
			'setting' => 'N/A',
			'message' => tra('You are using neither APC, WinCache nor xCache as your ByteCode Cache which would increase performance, if correctly configured. See Admin->Performance in your Tiki for more details.')
		);
	} else {
		$php_properties['ByteCode Cache'] = array(
			'fitness' => tra('info'),
			'setting' => 'N/A',
			'message' => tra('You are using neither APC nor xCache as your ByteCode Cache which would increase performance, if correctly configured. See Admin->Performance in your Tiki for more details.')
		);
	}
}

// memory_limit
$memory_limit = ini_get('memory_limit');
$s = trim($memory_limit);
$last = strtolower($s{strlen($s)-1});
switch ( $last ) {
	case 'g': $s *= 1024;
	case 'm': $s *= 1024;
	case 'k': $s *= 1024;
}
if ($s >= 160 * 1024 * 1024) {
	$php_properties['memory_limit'] = array(
		'fitness' => tra('good'),
		'setting' => $memory_limit,
		'message' => tra('Your memory_limit is at').' '.$memory_limit.'. '.tra('This is known to behave well even for bigger sites.')
	);
} elseif ( $s < 160 * 1024 * 1024 && $s > 127 * 1024 * 1024 ) {
	$php_properties['memory_limit'] = array(
		'fitness' => tra('ugly') ,
		'setting' => $memory_limit,
		'message' => tra('Your memory_limit is at').' '.$memory_limit.'. '.tra('This will normally work, but you might run into problems when your site grows.')
	);
} else {
	$php_properties['memory_limit'] = array(
		'fitness' => tra('bad'),
		'setting' => $memory_limit,
		'message' => tra('Your memory_limit is at').' '.$memory_limit.'. '.tra('This is known to cause issues! You should raise your memory_limit to at least 128M, which is the default of PHP.')
	);
}

// session.save_handler
$s = ini_get('session.save_handler');
if ($s != 'files') {
	$php_properties['session.save_handler'] = array(
		'fitness' => tra('bad'),
		'setting' => $s,
		'message' => tra('Your session.save_handler must be set to \'files\'.')
	);
} else {
	$php_properties['session.save_handler'] = array(
		'fitness' => tra('good'),
		'setting' => $s,
		'message' => tra('Well set! the default setting of \'files\' is needed for Tiki.')
	);
}

// zlib.output_compression
$s = ini_get('zlib.output_compression');
if ($s) {
	$php_properties['zlib.output_compression'] = array(
		'fitness' => tra('info'),
		'setting' => 'On',
		'message' => tra('You have zlib output compression turned on. This saves bandwidth. Turning it off would in turn reduce CPU usage. Choose your poison.')
	);
} else {
	$php_properties['zlib.output_compression'] = array(
		'fitness' => tra('info'),
		'setting' => 'Off',
		'message' => tra('You have zlib output compression turned off. This reduces CPU usage. Turning it on would in turn save bandwidth. Choose your poison.')
	);
}

// register globals
$s = ini_get('register_globals');
if ($s) {
	$php_properties['register_globals'] = array(
		'fitness' => tra('bad'),
		'setting' => 'On',
		'message' => tra('register_globals should be off by default. See the PHP manual for details.')
	);
} else {
	$php_properties['register_globals'] = array(
		'fitness' => tra('good'),
		'setting' => 'Off',
		'message' => tra('Well set! And you are future proof also as register_globals is deprecated.')
	);
}

// safe mode
$s = ini_get('safe_mode');
if ($s) {
	$php_properties['safe_mode'] = array(
		'fitness' => tra('bad'),
		'setting' => 'On',
		'message' => tra('safe_mode is deprecated and should be off by default. See the <a href="http://www.php.net/manual/de/features.safe-mode.php">PHP manual</a> for details.')
	);
} else {
	$php_properties['safe_mode'] = array(
		'fitness' => tra('good'),
		'setting' => 'Off',
		'message' => tra('Well set! And you are future proof also as safe_mode is deprecated.')
	);
}

// magic_quotes_gpc
$s = ini_get('magic_quotes_gpc');
if ($s) {
	$php_properties['magic_quotes_gpc'] = array(
		'fitness' => tra('bad'),
		'setting' => 'On',
		'message' => tra('Some features like assigning perms to a group with a name containing a quote will not work with this being on. magic_quotes_gpc is also deprecated and should be off by default. See the PHP manual for details. You may experience weird behaviour of your Tiki.')
	);
} else {
	$php_properties['magic_quotes_gpc'] = array(
		'fitness' => tra('good'),
		'setting' => 'Off',
		'message' => tra('Well set! Some features like assigning perms to a group with a name containing a quote will not work with this being on. And you are future proof also as magic_quotes_gpc is deprecated.')
	);
}

// default_charset
$s = ini_get('default_charset');
if ( strtolower($s) == 'utf-8' ) {
	$php_properties['default_charset'] = array(
		'fitness' => tra('good'),
		'setting' => $s,
		'message' => tra('Well set! Tiki is fully UTF-8 and so should your installation be.')
	);
} else {
	$php_properties['default_charset'] = array(
		'fitness' => tra('ugly'),
		'setting' => $s,
		'message' => tra('default_charset should be UTF-8 as Tiki is fully UTF-8. Please check your php.ini.')
	);
}

// date.timezone
$s = ini_get('date.timezone');
if ( empty($s) ) {
	$php_properties['date.timezone'] = array(
		'fitness' => tra('ugly'),
		'setting' => $s,
		'message' => tra('You have no timezone set! While there are a lot of fallbacks in PHP to determine the timezone, the only reliable solution is to set it explicitly in php.ini! Please check the value of date.timezone in php.ini.')
	);
} else {
	$php_properties['date.timezone'] = array(
		'fitness' => tra('good'),
		'setting' => $s,
		'message' => tra('Well done! Having a timezone set protects you from many weird errors.')
	);
}

// file_uploads
$s = ini_get('file_uploads');
if ($s) {
	$php_properties['file_uploads'] = array(
		'fitness' => tra('good'),
		'setting' => 'On',
		'message' => tra('You can upload files to your Tiki.')
	);
} else {
	$php_properties['file_uploads'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Off',
		'message' => tra('You will not be able to upload any files to your Tiki.')
	);
}

// max_execution_time
$s = ini_get('max_execution_time');
if ( $s >= 30 && $s <= 90 ) {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('good'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('This is a good value for production sites. If you experience timeouts (such as when performing admin functions) you may need to increase this nevertheless.')
	);
} elseif ( $s == -1 || $s == 0 ) {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('ugly'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is unlimited.').' '.tra('This is not necessarily bad, but it\'s a good idea to limit this time on productions servers in order to eliminate unexpectedly long running scripts.')
	);
} elseif ( $s > 90 ) {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('ugly'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('This is not necessarily bad, but it\'s a good idea to limit this time on productions servers in order to eliminate unexpectedly long running scripts.')
	);
} else {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('bad'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('It is likely that some scripts, e.g. admin functions will not finish in this time! You should raise your max_execution_time to at least 30s.')
	);
}

// max_input_time
$s = ini_get('max_input_time');
if ( $s >= 50 && $s <= 90 ) {
	$php_properties['max_input_time'] = array(
		'fitness' => tra('good'),
		'setting' => $s.'s',
		'message' => tra('Your max_input_time is at').' '.$s.'. '.tra('This is a good value for production sites. If you experience timeouts (such as when performing admin functions) you may need to increase this nevertheless.')
	);
} elseif ( $s == -1 || $s == 0 ) {
	$php_properties['max_input_time'] = array(
		'fitness' => tra('ugly'),
		'setting' => $s.'s',
		'message' => tra('Your max_input_time is unlimited.').' '.tra('This is not necessarily bad, but it\'s a good idea to limit this time on productions servers in order to eliminate unexpectedly long running scripts.')
	);
} elseif ( $s > 90 ) {
	$php_properties['max_input_time'] = array(
		'fitness' => tra('ugly'),
		'setting' => $s.'s',
		'message' => tra('Your max_input_time is at').' '.$s.'. '.tra('This is not necessarily bad, but it\'s a good idea to limit this time on productions servers in order to eliminate unexpectedly long running scripts.')
	);
} else {
	$php_properties['max_input_time'] = array(
		'fitness' => tra('bad'),
		'setting' => $s.'s',
		'message' => tra('Your max_input_time is at').' '.$s.'. '.tra('It is likely that some scripts, e.g. admin functions will not finish in this time! You should raise your max_input_time to at least 30 seconds.')
	);
}

// upload_max_filesize
$upload_max_filesize = ini_get('upload_max_filesize');
$s = trim($upload_max_filesize);
$last = strtolower($s{strlen($s)-1});
switch ( $last ) {
	case 'g': $s *= 1024;
	case 'm': $s *= 1024;
	case 'k': $s *= 1024;
}
if ($s >= 8 * 1024 * 1024) {
	$php_properties['upload_max_filesize'] = array(
		'fitness' => tra('good'),
		'setting' => $upload_max_filesize,
		'message' => tra('Your upload_max_filesize is at').' '.$upload_max_filesize.'. '.tra('You can upload quite big files, but keep in mind to set your script timeouts accordingly.')
	);
} else {
	$php_properties['upload_max_filesize'] = array(
		'fitness' => tra('ugly'),
		'setting' => $upload_max_filesize,
		'message' => tra('Your upload_max_filesize is at').' '.$upload_max_filesize.'. '.tra('Nothing wrong with that, but some users might want to upload something bigger.')
	);
}

// post_max_size
$post_max_size = ini_get('post_max_size');
$s = trim($post_max_size);
$last = strtolower($s{strlen($s)-1});
switch ( $last ) {
	case 'g': $s *= 1024;
	case 'm': $s *= 1024;
	case 'k': $s *= 1024;
}
if ($s >= 8 * 1024 * 1024) {
	$php_properties['post_max_size'] = array(
		'fitness' => tra('good'),
		'setting' => $post_max_size,
		'message' => tra('Your post_max_size is at').' '.$post_max_size.'. '.tra('You can upload quite big files, but keep in mind to set your script timeouts accordingly.')
	);
} else {
	$php_properties['post_max_size'] = array(
		'fitness' => tra('ugly'),
		'setting' => $post_max_size,
		'message' => tra('Your post_max_size is at').' '.$post_max_size.'. '.tra('Nothing wrong with that, but some users might want to upload something bigger.')
	);
}

// PHP Extensions
// fileinfo
$s = extension_loaded('fileinfo');
if ($s) {
	$php_properties['fileinfo'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra("The fileinfo extension is needed for the 'Validate uploaded file content' preference.")
	);
} else {
	$php_properties['fileinfo'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'Not available',
		'message' => tra("The fileinfo extension is needed for the 'Validate uploaded file content' preference.")
	);
}

// GD
$s = extension_loaded('gd');
if ( $s && function_exists('gd_info') ) {
	$gd_info = gd_info();
	$im = $ft = null;
	if (function_exists('imagecreate')) {
		$im = @imagecreate(110, 20);
	}
	if (function_exists('imageftbbox')) {
		$ft = @imageftbbox(12, 0, './lib/captcha/DejaVuSansMono.ttf', 'test');
	}
	if ($im && $ft) {
		$php_properties['gd'] = array(
			'fitness' => tra('good'),
			'setting' => $gd_info['GD Version'],
			'message' => tra('The GD extension is needed for manipulation of images, e.g. also for CAPTCHAs.')
		);
		imagedestroy($im);
	} else if ($im) {
		$php_properties['gd'] = array(
				'fitness' => tra('ugly'),
				'setting' => $gd_info['GD Version'],
				'message' => tra('The GD extension is loaded, and Tiki can create images, but the FreeType extension is needed for CAPTCHA text generation.')
			);
			imagedestroy($im);
	} else {
		$php_properties['gd'] = array(
			'fitness' => tra('ugly'),
			'setting' => 'Dysfunctional',
			'message' => tra('The GD extension is loaded, but Tiki is unable to create images. Please check your GD library configuration.')
		);
	}
} else {
	$php_properties['gd'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('The GD extension is needed for manipulation of images, e.g. also for CAPTCHAs.')
	);
}

// Image Magick
$s = class_exists('Imagick');
if ( $s ) {
	$image = new Imagick();
	$image->newImage(100, 100, new ImagickPixel('red'));
	if ( $image ) {
		$php_properties['Image Magick'] = array(
			'fitness' => tra('good'),
			'setting' => 'Available',
			'message' => tra('ImageMagick is used as a fallback in case that GD is not available.')
		);
		$image->destroy();
	} else {
		$php_properties['Image Magick'] = array(
			'fitness' => tra('ugly'),
			'setting' => 'Dysfunctional',
			'message' => tra('ImageMagick is used as a fallback in case that GD is not available.').tra('ImageMagick is available, but unable to create images. Please check your ImageMagick configuration.')
			);
	}
} else {
	$php_properties['Image Magick'] = array(
		'fitness' => tra('info'),
		'setting' => 'Not Available',
		'message' => tra('ImageMagick is used as a fallback in case that GD is not available.')
		);
}

// mbstring
$s = extension_loaded('mbstring');
if ($s) {
	$func_overload = ini_get('mbstring.func_overload');
	if ($func_overload == 0 && function_exists('mb_split')) {
		$php_properties['mbstring'] = array(
			'fitness' => tra('good'),
			'setting' => 'Loaded',
			'message' => tra('The mbstring extension is needed for an UTF-8 compatible lower case filter in the admin search for example.')
		);
	} elseif ($func_overload != 0) {
		$php_properties['mbstring'] = array(
			'fitness' => tra('ugly'),
			'setting' => 'Badly configured',
			'message' => tra('The mbstring extension is loaded, but mbstring.func_overload = '.' '.$func_overload.'.'.' '.'Tiki only works with mbsring.func_overload = 0. Please check your php.ini.')
		);
	} else {
		$php_properties['mbstring'] = array(
			'fitness' => tra('bad'),
			'setting' => 'Badly installed',
			'message' => tra('The mbstring extension is loaded, but missing important functions as for example mb_split(). You need to reinstall it with --enable-mbregex or ask your hoster do do it.')
		);
	}
} else {
	$php_properties['mbstring'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('The mbstring extension is needed for an UTF-8 compatible lower case filter.')
	);
}

// calendar
$s = extension_loaded('calendar');
if ($s) {
	$php_properties['calendar'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('calendar extension is needed by Tiki.')
	);
} else {
	$php_properties['calendar'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('calendar extension is needed by Tiki.').' '.tra('You will not be able to use the calendar feature of Tiki.')
	);
}

// ctype
$s = extension_loaded('ctype');
if ($s) {
	$php_properties['ctype'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('ctype extension is needed by Tiki.')
	);
} else {
	$php_properties['ctype'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('ctype extension is needed by Tiki.')
	);
}

// libxml
$s = extension_loaded('libxml');
if ($s) {
	$php_properties['libxml'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('This extension is needed for WebDAV.')
	);
} else {
	$php_properties['libxml'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('This extension is needed for WebDAV.')
	);
}

$s = extension_loaded('ldap');
if ($s) {
	$php_properties['LDAP'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('This extension is needed to connect your Tiki to an LDAP server. More info at: http://doc.tiki.org/LDAP ')
	);
} else {
	$php_properties['LDAP'] = array(
		'fitness' => tra('info'),
		'setting' => 'Not available',
		'message' => tra('You will not be able to connect your Tiki to an LDAP server as the needed PHP extension is missing. More info at: http://doc.tiki.org/LDAP')
	);
}

$s = extension_loaded('memcache');
if ($s) {
	$php_properties['memcache'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('This extension can be used to speed up your Tiki by saving sessions as well as wiki and forum data on a memcached server.')
	);
} else {
	$php_properties['memcache'] = array(
		'fitness' => tra('info'),
		'setting' => 'Not available',
		'message' => tra('This extension can be used to speed up your Tiki by saving sessions as well as wiki and forum data on a memcached server.')
	);
}

/*
*	If TortoiseSVN 1.7 is used, it uses an sqlite database to store the SVN info. sqlite3 extention needed to read svn info.
*/
if (is_file('.svn/wc.db')) {
	// It's an TortoiseSVN 1.7+ installation
	$s = extension_loaded('sqlite3');
	if ($s) {
		$php_properties['sqlite3'] = array(
			'fitness' => tra('good'),
			'setting' => 'Loaded',
			'message' => tra('This extension is used to interpret SVN information for TortoiseSVN 1.7 or higher.')
			);
	} else {
		$php_properties['sqlite3'] = array(
			'fitness' => tra('ugly'),
			'setting' => 'Not available',
			'message' => tra('This extension is used to interpret SVN information for TortoiseSVN 1.7 or higher.')
			);
	}
}

// Check for existence of eval()
// eval() is a language construct and not a function
// so function_exists() doesn't work
$s = eval('return 42;');
if ( $s == 42 ) {
	$php_properties['eval()'] = array(
		'fitness' => tra('good'),
		'setting' => 'Available',
		'message' => tra('The eval() function is required by the Smarty templating engine.')
	);
} else {
	$php_properties['eval()'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('The eval() function is required by the Smarty templating engine.').' '.tra('You will get "Please contact support about" messages instead of modules. eval() is most probably disabled via Suhosin.')
	);
}

// Zip Archive class
$s = class_exists('ZipArchive');
if ( $s ) {
	$php_properties['ZipArchive class'] = array(
		'fitness' => tra('good'),
		'setting' => 'Available',
		'message' => tra('The ZipArchive class is needed for features such as XML Wiki Import/Export and PluginArchiveBuilder.')
		);
} else {
	$php_properties['ZipArchive class'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'Not Available',
		'message' => tra('The ZipArchive class is needed for features such as XML Wiki Import/Export and PluginArchiveBuilder.')
		);
}

// DateTime class
$s = class_exists('DateTime');
if ( $s ) {
	$php_properties['DateTime class'] = array(
		'fitness' => tra('good'),
		'setting' => 'Available',
		'message' => tra('The DateTime class is needed for the WebDAV feature.')
		);
} else {
	$php_properties['DateTime class'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'Not Available',
		'message' => tra('The DateTime class is needed for the WebDAV feature.')
		);
}

// Get MySQL properties and check them
$mysql_properties = false;
$mysql_variables = false;
if ($connection || !$standalone) {

	// MySQL version
	$query = 'SELECT VERSION();';
	$result = query($query, $connection);
	$mysql_version = $result[0]['VERSION()'];
	$s = version_compare($mysql_version, '5.0.2', '>=');
	if ( $s == true ) {
		$mysql_properties['Version'] = array(
			'fitness' => tra('good'),
			'setting' => $mysql_version,
			'message' => tra('Tiki requires MySQL >= 5.x.')
		);
	} else {
		$mysql_properties['Version'] = array(
			'fitness' => tra('bad'),
			'setting' => $mysql_version,
			'message' => tra('Tiki requires MySQL >= 5.x.')
		);
	}

	// max_allowed_packet
	$query = "SHOW VARIABLES LIKE 'max_allowed_packet'";
	$result = query($query, $connection);
	$s = $result[0]['Value'];
	$max_allowed_packet = $s / 1024 / 1024;
	if ($s >= 8 * 1024 * 1024) {
		$mysql_properties['max_allowed_packet'] = array(
			'fitness' => tra('good'),
			'setting' => $max_allowed_packet.'M',
			'message' => tra('Your max_allowed_packet setting is at').' '.$max_allowed_packet.'M. '.tra('You can upload quite big files, but keep in mind to set your script timeouts accordingly.').' '.tra('This limits the size of binary files that can be added to Tiki, when storing files in the database. Please see: <a href="http://doc.tiki.org/File+Storage">file storage</a>.')
		);
	} else {
		$mysql_properties['max_allowed_packet'] = array(
			'fitness' => tra('ugly'),
			'setting' => $max_allowed_packet.'M',
			'message' => tra('Your max_allowed_packet setting is at').' '.$max_allowed_packet.'M. '.tra('Nothing wrong with that, but some users might want to upload something bigger.').' '.tra('This limits the size of binary files that can be added to Tiki, when storing files in the database. Please see: <a href="http://doc.tiki.org/File+Storage">file storage</a>.')
		);
	}

	// UTF-8 Charset
	$charset_types = "client connection database results server system";
	foreach (explode(' ', $charset_types) as $type) {
		$query = "SHOW VARIABLES LIKE 'character_set_".$type."';";
		$result = query($query, $connection);
		foreach ($result as $value) {
			if ( $value['Value'] == 'utf8' ) {
				$mysql_properties[$value['Variable_name']] = array(
					'fitness' => tra('good'),
					'setting' => $value['Value'],
					'message' => tra('Tiki is fully UTF-8 and so should every part of your stack be.')
				);
			} else {
				$mysql_properties[$value['Variable_name']] = array(
					'fitness' => tra('ugly'),
					'setting' => $value['Value'],
					'message' => tra('On a fresh install you should have everything set to UTF-8 to not run into any suprises. For further information please see <a href="http://doc.tiki.org/Understanding+Encoding">Understanding Encoding</a>.')
				);
			}

		}
	}
	// UTF-8 Collation
	$collation_types = "connection database server";
	foreach (explode(' ', $collation_types) as $type) {
		$query = "SHOW VARIABLES LIKE 'collation_".$type."';";
		$result = query($query, $connection);
		foreach ($result as $value) {
			if ( substr($value['Value'], 0, 4) == 'utf8' ) {
				$mysql_properties[$value['Variable_name']] = array(
					'fitness' => tra('good'),
					'setting' => $value['Value'],
					'message' => tra('Tiki is fully UTF-8 and so should every part of your stack be. utf8_unicode_ci is the default collation for Tiki.')
				);
			} else {
				$mysql_properties[$value['Variable_name']] = array(
					'fitness' => tra('ugly'),
					'setting' => $value['Value'],
					'message' => tra('On a fresh install you should have everything set to UTF-8 to not run into any suprises. utf8_unicode_ci is the default collation for Tiki. For further information please see <a href="http://doc.tiki.org/Understanding+Encoding">Understanding Encoding</a>.')
				);
			}

		}
	}

	// slow_query_log
	$query = "SHOW VARIABLES LIKE 'slow_query_log'";
	$result = query($query, $connection);
	$s = $result[0]['Value'];
	if ($s == 'OFF') {
		$mysql_properties['slow_query_log'] = array(
			'fitness' => tra('info'),
			'setting' => $s,
			'message' => tra('Your MySQL doesn\'t log slow queries. If you have performance issues, you might want to enable this, but keep in mind that the logging itself slows MySQL down.')
		);
	} else {
		$mysql_properties['slow_query_log'] = array(
			'fitness' => tra('info'),
			'setting' => $s,
			'message' => tra('Your MySQL logs slow queries. If you don\'t have performance issues, you should disable this on a production site as it slows MySQL down.')
		);
	}

	// MySQL Variables
	$query = "SHOW VARIABLES;";
	$result = query($query, $connection);
	foreach ($result as $value) {
		$mysql_variables[$value['Variable_name']] = array('value' => $value['Value']);
	}

	if (!$standalone) {
		$mysql_crashed_tables = array();
		// This should give all crashed tables (MyISAM at least) - does need testing though !!
		$query = 'SHOW TABLE STATUS WHERE engine IS NULL AND comment <> "VIEW";';
		$result = query($query, $connection);
		foreach ($result as $value) {
			$mysql_crashed_tables[$value['Name']] = array('Comment' => $value['Comment']);
		}
	}
}

// Apache properties

$apache_properties = false;
if ( function_exists('apache_get_version')) {

	// Apache Modules
	$apache_modules = apache_get_modules();

	// mod_rewrite
	$s = false;
	$s = array_search('mod_rewrite', $apache_modules);
	if ($s) {
		$apache_properties['mod_rewrite'] = array(
			'setting' => 'Loaded',
			'fitness' => tra('good') ,
			'message' => tra('Tiki needs this module for Search Engine Friendly URLs via .htaccess. We can\'t check though, if your web server respects configurations made in .htaccess. For further information go to Admin->SefURL in your Tiki.')
		);
	} else {
		$apache_properties['mod_rewrite'] = array(
			'setting' => 'Not available',
			'fitness' => tra('ugly') ,
			'message' => tra('Tiki needs this module for Search Engine Friendly URLs. For further information go to Admin->SefURL in your Tiki.')
		);
	}

	if (!$standalone) {
		// work out if RewriteBase is set up properly
		global $url_path;
		$enabledFileName = '.htaccess';
		if (file_exists($enabledFileName)) {
			$enabledFile = fopen($enabledFileName, "r");
			$rewritebase = '/';
			while ($nextLine = fgets($enabledFile)) {
				if (preg_match('/^RewriteBase\s*(.*)$/', $nextLine, $m)) {
					$rewritebase = substr($m[1], -1) !== '/' ? $m[1] . '/' : $m[1];
					break;
				}
			}
			if ($url_path == $rewritebase) {
				$smarty->assign('rewritebaseSetting', $rewritebase);
				$apache_properties['RewriteBase'] = array(
					'setting' => $rewritebase,
					'fitness' => tra('good') ,
					'message' => tra('Your RewriteBase is set correctly in .htaccess. Search Engine Friendly URLs should work. Beware though that we can\'t check if Apache really loads .htaccess.')
				);
			} else {
				$apache_properties['RewriteBase'] = array(
					'setting' => $rewritebase,
					'fitness' => tra('bad') ,
					'message' => tra('Your RewriteBase is not set correctly in .htaccess. Search Engine Friendly URLs are not going to work like that. It should be set to "').substr($url_path, 0, -1).'".'
				);
			}
		} else {
			$apache_properties['RewriteBase'] = array(
				'setting' => $rewritebase,
				'fitness' => tra('info') ,
				'message' => tra('You haven\'t activated .htaccess. So this check is useless. If you want to use Search Engine Friendly URLs, you will have to activate .htaccess by copying _htaccess into its place (or a symlink if supported by your Operating System). Then come back to have a look at this check again.')
			);
		}
	}

	// mod_expires
	$s = false;
	$s = array_search('mod_expires', $apache_modules);
	if ($s) {
		$apache_properties['mod_expires'] = array(
			'setting' => 'Loaded',
			'fitness' => tra('good') ,
			'message' => tra('With this module you can set the HTTP Expires header and therefore increase performance. We can\'t check though, if mod_expires is configured correctly.')
		);
	} else {
		$apache_properties['mod_expires'] = array(
			'setting' => 'Not available',
			'fitness' => tra('ugly') ,
			'message' => tra('With this module you can set the HTTP Expires header and therefore increase performance. Once you install it, you still need to configure it correctly.')
		);
	}

	// mod_deflate
	$s = false;
	$s = array_search('mod_deflate', $apache_modules);
	if ($s) {
		$apache_properties['mod_deflate'] = array(
			'setting' => 'Loaded',
			'fitness' => tra('good') ,
			'message' => tra('With this module you can compress the data your webserver sends out and therefore decrease used bandwidth and increase performance. We can\'t check though, if mod_deflate is configured correctly.')
		);
	} else {
		$apache_properties['mod_deflate'] = array(
			'setting' => 'Not available',
			'fitness' => tra('ugly') ,
			'message' => tra('With this module you can compress the data your webserver sends out and therefore decrease used bandwidth and increase performance. Once you install it, you still need to configure it correctly.')
		);
	}

	// mod_security
	$s = false;
	$s = array_search('mod_security', $apache_modules);
	if ($s) {
		$apache_properties['mod_security'] = array(
			'setting' => 'Loaded',
			'fitness' => tra('info') ,
			'message' => tra('This module can increase security of your Tiki and therefore your server, but be warned that it is very tricky to configure it correctly. A misconfiguration can lead to failed page saves or other hard to trace bugs.')
		);
	} else {
		$apache_properties['mod_security'] = array(
			'setting' => 'Not available',
			'fitness' => tra('info') ,
			'message' => tra('This module can increase security of your Tiki and therefore your server, but be warned that it is very tricky to configure it correctly. A misconfiguration can lead to failed page saves or other hard to trace bugs.')
		);
	}

	// Get /server-info, if available
	if (function_exists('curl_init') && function_exists('curl_exec')) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'http://localhost/server-info');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
		$apache_server_info = curl_exec($curl);
		if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
			$apache_server_info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $apache_server_info);
		} else {
			$apache_server_info = false;
		}
		curl_close($curl);
	} else {
		$apache_server_info = 'nocurl';
	}
}


// IIS Properties
$iis_properties = false;

if (check_isIIS()) {

	// IIS Rewrite module
	if (check_hasIIS_UrlRewriteModule()) {
		$iis_properties['IIS Url Rewrite Module'] = array(
			'fitness' => tra('good'),
			'setting' => 'Available',
			'message' => tra('The URL Rewrite Module is required to use SEFURL on IIS.')
			);
	} else {
		$iis_properties['IIS Url Rewrite Module'] = array(
			'fitness' => tra('bad'),
			'setting' => 'Not Available',
			'message' => tra('The URL Rewrite Module is required to use SEFURL on IIS.')
			);
	}
}



// Security Checks
// get all dangerous php settings and check them
$security = false;

// check file upload dir and compare it to tiki root dir
$s = ini_get('upload_tmp_dir');
$sn = substr($_SERVER['SCRIPT_NAME'], 0, -14);
if ( $s != "" && strpos($sn, $s) !== false) {
	$security['upload_tmp_dir'] = array(
		'fitness' => tra('unsafe') ,
		'setting' => $s,
		'message' => tra('upload_tmp_dir is probably within your Tiki directory. There is a risk that someone can upload any file to this directory and access them via web browser')
	);
} else {
	$security['upload_tmp_dir'] = array(
		'fitness' => tra('unknown') ,
		'setting' => $s,
		'message' => tra('Can\'t reliably determine, if your upload_tmp_dir is accessible via a web browser. To make sure you should check your webserver config.')
	);
}

// register globals
$s = ini_get('register_globals');
if ($s) {
	$security['register_globals'] = array(
		'setting' => 'On',
		'fitness' => tra('unsafe') ,
		'message' => tra('register_globals should be off by default. See the php manual for details.')
	);
} else {
	$security['register_globals'] = array(
		'setting' => 'Off',
		'fitness' => tra('safe') ,
		'message' => tra('register_globals should be off by default. See the php manual for details.')
	);
}

// Determine system state
$pdf_webkit = '';
if (isset($prefs) && $prefs['print_pdf_from_url'] == 'webkit') {
	$pdf_webkit = '<b>'.tra('WebKit is enabled').'.</b> ';
}
$feature_blogs = '';
if (isset($prefs) && $prefs['feature_blogs'] == 'y') {
	$feature_blogs = '<b>'.tra('Blogs is enabled').'.</b> ';
}

$fcts = array(
		array (
			'function' => 'exec',
			'risky' => tra('Exec can potentially be used to execute arbitrary code on your server.').' '.tra('Tiki does not need it, you may want to disable it.'),
			'safe' => tra('Exec can be potentially be used to execute arbitrary code on your server.').' '.tra('Tiki does not need it, you are wise to have it disabled.')
		),
		array (
			'function' => 'passthru',
			'risky' => tra('Passthru is similar to exec.').' '.tra('Tiki does not need it, you may want to disable it.'),
			'safe' =>  tra('Passthru is similar to exec.').' '.tra('Tiki does not need it, you are wise to have it disabled.')
		),
		array (
			'function' => 'shell_exec',
			'risky' => tra('Shell_exec is similar to exec.').' '.tra('Tiki needs it to run PDF from URL: WebKit (wkhtmltopdf). '.$pdf_webkit.'If you need this and trust the other PHP software on your server, you should enable it.'),
			'safe' =>  tra('Shell_exec is similar to exec.').' '.tra('Tiki needs it to run PDF from URL: WebKit (wkhtmltopdf). '.$pdf_webkit.'If you need this and trust the other PHP software on your server, you should enable it.')
		),
		array (
			'function' => 'system',
			'risky' => tra('System is similar to exec.').' '.tra('Tiki does not need it, you may want to disable it.'),
			'safe' =>  tra('System is similar to exec.').' '.tra('Tiki does not need it, you are wise to have it disabled.')
		),
		array (
			'function' => 'proc_open',
			'risky' => tra('Proc_open is similar to exec.').' '.tra('Tiki does not need it, you may want to disable it.'),
			'safe' =>  tra('Proc_open is similar to exec.').' '.tra('Tiki does not need it, you are wise to have it disabled.')
		),
		array (
			'function' => 'curl_exec',
			'risky' => tra('Curl_exec can potentially be abused to write malicious code.').' '.tra('Tiki needs it to run features like Kaltura, CAS login, CClite and the myspace and sf wiki-plugins. If you need these and trust the other PHP software on your server, you should enable it.'),
			'safe' => tra('Curl_exec can potentially be abused to write malicious code.').' '.tra('Tiki needs it to run features like Kaltura, CAS login, CClite and the myspace and sf wiki-plugins. If you need these and trust the other PHP software on your server, you should enable it.')
		),
		array (
			'function' => 'curl_multi_exec',
			'risky' => tra('Curl_multi_exec can potentially be abused to write malicious code.').' '.tra('Tiki needs it to run features like Kaltura, CAS login, CClite and the myspace and sf wiki-plugins. If you need these and trust the other PHP software on your server, you should enable it.'),
			'safe' => tra('Curl_multi_exec can potentially be abused to write malicious code.').' '.tra('Tiki needs it to run features like Kaltura, CAS login, CClite and the myspace and sf wiki-plugins. If you need these and trust the other PHP software on your server, you should enable it.')
		),
		array (
			'function' => 'parse_ini_file',
			'risky' => tra('It is probably an urban myth that this is dangerous. Tiki team will reconsider this check, but be warned.'),
			'safe' => tra('It is probably an urban myth that this is dangerous. Tiki team will reconsider this check, but be warned.'),
		),
		array (
			'function' => 'show_source',
			'risky' => tra('It is probably an urban myth that this is dangerous. Tiki team will reconsider this check, but be warned.'),
			'safe' => tra('It is probably an urban myth that this is dangerous. Tiki team will reconsider this check, but be warned.'),
		)
	);

foreach ($fcts as $fct) {
	if (function_exists($fct['function'])) {
		$security[$fct['function']] = array(
			'setting' => tra('Enabled'),
			'fitness' => tra('risky'),
			'message' => $fct['risky']
		);
	} else {
		$security[$fct['function']] = array(
			'setting' => tra('Disabled'),
			'fitness' => tra('safe'),
			'message' => $fct['safe']
		);
	}
}

// trans_sid
$s = ini_get('session.use_trans_sid');
if ($s) {
	$security['session.use_trans_sid'] = array(
		'setting' => 'Enabled',
		'fitness' => tra('unsafe'),
		'message' => tra('session.use_trans_sid should be off by default. See the php manual for details.')
	);
} else {
	$security['session.use_trans_sid'] = array(
		'setting' => 'Disabled',
		'fitness' => tra('safe'),
		'message' => tra('session.use_trans_sid should be off by default. See the php manual for details.')
	);
}

$s = ini_get('xbithack');
if ($s == 1) {
	$security['xbithack'] = array(
		'setting' => 'Enabled',
		'fitness' => tra('unsafe'),
		'message' => tra('setting the xbithack option is unsafe. Depending on the file handling of your webserver and your tiki settings, it may be possible that an attacker can upload scripts to file gallery and execute them')
	);
} else {
	$security['xbithack'] = array(
		'setting' => 'Disabled',
		'fitness' => tra('safe'),
		'message' => tra('setting the xbithack option is unsafe. Depending on the file handling of your webserver and your tiki settings, it may be possible that an attacker can upload scripts to file gallery and execute them')
	);
}

$s = ini_get('allow_url_fopen');
if ($s == 1) {
	$security['allow_url_fopen'] = array(
		'setting' => 'Enabled',
		'fitness' => tra('risky'),
		'message' => tra('allow_url_fopen may potentially be used to upload remote data or scripts. '.$feature_blogs.'If you dont use the blog feature, you can switch it off.')
	);
} else {
	$security['allow_url_fopen'] = array(
		'setting' => 'Disabled',
		'fitness' => tra('safe'),
		'message' => tra('allow_url_fopen may potentially be used to upload remote data or scripts. '.$feature_blogs.'If you dont use the blog feature, you can switch it off.')
	);
}

if (!$standalone) {
	// The following is borrowed from tiki-admin_system.php
	if ($prefs['feature_forums'] == 'y') {
		include_once ('lib/comments/commentslib.php');
		$commentslib = new Comments($dbTiki);
		$dirs = $commentslib->list_directories_to_save();
	} else {
		$dirs = array();
	}
	if ($prefs['feature_galleries'] == 'y' && !empty($prefs['gal_use_dir'])) {
		$dirs[] = $prefs['gal_use_dir'];
	}
	if ($prefs['feature_file_galleries'] == 'y' && !empty($prefs['fgal_use_dir'])) {
		$dirs[] = $prefs['fgal_use_dir'];
	}
	if ($prefs['feature_trackers'] == 'y') {
		if (!empty($prefs['t_use_dir'])) {
			$dirs[] = $prefs['t_use_dir'];
		}
		$dirs[] = 'img/trackers';
	}
	if ($prefs['feature_wiki'] == 'y') {
		if (!empty($prefs['w_use_dir'])) {
			$dirs[] = $prefs['w_use_dir'];
		}
		if ($prefs['feature_create_webhelp'] == 'y') {
			$dirs[] = 'whelp';
		}
		$dirs[] = 'img/wiki';
		$dirs[] = 'img/wiki_up';
	}
	if ($prefs['feature_maps'] && !empty($prefs['map_path'])) {
		$dirs[] = $prefs['map_path'];
	}
	$dirs = array_unique($dirs);
	$dirsExist = array();
	foreach ($dirs as $i => $d) {
		$dirsWritable[$i] = is_writable($d);
	}
	$smarty->assign_by_ref('dirs', $dirs);
	$smarty->assign_by_ref('dirsWritable', $dirsWritable);
}

if ($standalone) {
	$render .= '<style type="text/css">td, th { border: 1px solid #000000; vertical-align: baseline;}</style>';
//	$render .= '<h1>Tiki Server Compatibility</h1>';
	$render .= '<h2>MySQL or MariaBD Database Properties</h2>';
	renderTable($mysql_properties);
	$render .= '<h2>Test sending e-mails</h2>';
	if (isset($_REQUEST['email_test_to'])) {
		$email_test_headers = 'From: noreply@tiki.org' . "\n";	// needs a valid sender
		$email_test_headers .= 'Reply-to: '. $_POST['email_test_to'] . "\n";
		$email_test_headers .= "Content-type: text/plain; charset=utf-8\n";
		$email_test_headers .= 'X-Mailer: Tiki-Check - PHP/' . phpversion() . "\n";
		$email_test_subject = tra('Test mail from Tiki Server Compatibility Test');
		$email_test_body = tra("Congratulations!\n\nYour server can send emails.\n\n");
		$email_test_body .= "\t".tra('Server:').' '.(empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['SERVER_NAME']) . "\n";
		$email_test_body .= "\t".tra('Sent:').' '.date(DATE_RFC822) . "\n";

		$sentmail = mail($_POST['email_test_to'], $email_test_subject, $email_test_body, $email_test_headers);
		if ($sentmail) {
			$mail['Sending mail'] = array(
				'setting' => 'Accepted',
				'fitness' => tra('good'),
				'message' => tra('We were able to send an e-mail. This only means that a mail server accepted the mail for delivery. We don\'t know, if that server really delivers the mail. Please check the inbox of '.$_POST['email_test_to'].' to see, if the delivery really works.')
			);
		} else {
			$mail['Sending mail'] = array(
				'setting' => 'Not accepted',
				'fitness' => tra('bad'),
				'message' => tra('We were not able to send an e-mail. It may be that there is no mail server installed on this machine or that it is badly configured. If you absolutely can\'t get the local mail server to work, you can setup a regular mail account and set SMTP settings in tiki-admin.php.')
			);
		}
		renderTable($mail);
	} else {
		$render .= '<form method="post" action="'.$_SERVER['REQUEST_URI'].'">';
		$render .= '<p><label for="e-mail">e-mail address to send test mail to</label>: <input type="text" id="email_test_to" name="email_test_to" /></p>';
		$render .= '<p><input type="submit" value=" Send e-mail " /></p>';
		$render .= '<p><input type="hidden" id="dbhost" name="dbhost" value="';
				if (isset($_POST['dbhost'])) {
					$render .= $_POST['dbhost'];
				};
			$render .= '" /></p>';
			$render .= '<p><input type="hidden" id="dbuser" name="dbuser" value="';
				if (isset($_POST['dbuser'])) {
					$render .= $_POST['dbuser'];
				};
			$render .= '"/></p>';
			$render .= '<p><input type="hidden" id="dbpass" name="dbpass" value="';
				if (isset($_POST['dbpass'])) {
					$render .= $_POST['dbpass'];
				};
			$render .= '"/></p>';
		$render .= '</form>';
	}

	$render .= '<h2>Server Information</h2>';
	renderTable($server_information);
	$render .= '<h2>Server Properties</h2>';
	renderTable($server_properties);
	$render .= '<h2>Apache properties</h2>';
	if ($apache_properties) {
		renderTable($apache_properties);
		if ($apache_server_info != 'nocurl' && $apache_server_info != false) {
			if (isset($_REQUEST['apacheinfo']) && $_REQUEST['apacheinfo'] == 'y') {
				$render .= $apache_server_info;
			} else {
				$render .= '<a href="'.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'&apacheinfo=y">Append Apache /server-info;</a>';
			}
		} elseif ($apache_server_info == 'nocurl') {
			$render .= 'You don\'t have the Curl extension in PHP, so we can\'t append Apache\'s server-info.';
		} else {
			$render .= 'Apparently you have not enabled mod_info in your Apache, so we can\'t append more verbose information to this output.';
		}
	} else {
		$render .= 'You are either not running the preferred Apache web server or you are running PHP with a SAPI that does not allow checking Apache properties (e.g. CGI or FPM).';
	}
	$render .= '<h2>IIS properties</h2>';
	if ($iis_properties) {
		renderTable($iis_properties);
	} else {
		$render .= "You are not running IIS web server.";
	}
	$render .= '<h2>PHP scripting language properties</h2>';
	renderTable($php_properties);
	$render .= '<h2>PHP security properties</h2>';
	renderTable($security);
	$render .= '<h2>MySQL Variables</h2>';
	renderTable($mysql_variables);
	$render .= '<h2>PHP Info</h2>';
	if ( isset($_REQUEST['phpinfo']) && $_REQUEST['phpinfo'] == 'y' ) {
		ob_start();
		phpinfo();
		$info = ob_get_contents();
		ob_end_clean();
		$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
		$render .= $info;
	} else {
		$render .= '<a href="'.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'].'&phpinfo=y">Append phpinfo();</a>';
	}
	createPage('Tiki Server Compatibility', $render);
} else {
	$smarty->assign_by_ref('server_information', $server_information);
	$smarty->assign_by_ref('server_properties', $server_properties);
	$smarty->assign_by_ref('mysql_properties', $mysql_properties);
	$smarty->assign_by_ref('php_properties', $php_properties);
	if ($apache_properties) {
		$smarty->assign_by_ref('apache_properties', $apache_properties);
	} else {
		$smarty->assign('no_apache_properties', 'You are either not running the preferred Apache web server or you are running PHP with a SAPI that does not allow checking Apache properties (e.g. CGI or FPM).');
	}
	if ($iis_properties) {
		$smarty->assign_by_ref('iis_properties', $iis_properties);
	} else {
		$smarty->assign('no_iis_properties', 'You are not running IIS web server.');
	}
	$smarty->assign_by_ref('security', $security);
	$smarty->assign_by_ref('mysql_variables', $mysql_variables);
	$smarty->assign_by_ref('mysql_crashed_tables', $mysql_crashed_tables);
	// disallow robots to index page:
	$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
	$smarty->assign('mid', 'tiki-check.tpl');
	$smarty->display('tiki.tpl');
}

function check_isIIS()
{
	static $IIS;
	// Sample value Microsoft-IIS/7.5
	if (!isset($IIS) && isset($_SERVER['SERVER_SOFTWARE'])) {
		$IIS = substr($_SERVER['SERVER_SOFTWARE'], 0, 13) == 'Microsoft-IIS';
	}
	return $IIS;
}

function check_hasIIS_UrlRewriteModule()
{
	return isset($_SERVER['IIS_UrlRewriteModule']) == true;
}
function createPage($title, $content)
{
	echo <<<END
<html
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="styles/fivealive.css" />
		<title>$title</title>
		<style type="text/css">
			table { border-collapse: collapse;}
			.button {
				border-radius: 3px 3px 3px 3px;
				font-size: 12.05px;
				font-weight: bold;
				padding: 2px 4px 3px;
				text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
				color: #FFF;
				text-transform: uppercase;
			}
			.ugly {background: #f89406;}
			.bad, .risky { background-color: #bd362f;}
			.good, .safe { background-color: #5bb75b;}
			.info {background-color: #2f96b4;}
//			h1 { border-bottom: 1px solid #DADADA; color: #7e7363; }
		</style>
	</head>
	<body class="tiki_wiki fixed_width">
	<div id="fixedwidth" class="fixedwidth">
		<div class="header_outer">
			<div class="header_container">
				<div class="clearfix fixedwidth header_fixedwidth">
					<header id="header" class="header">
					<div class="content clearfix modules" id="top_modules" style="min-height: 168px;">
						<div id="sitelogo" style="float: left">
							<img alt="Tiki Wiki CMS Groupware" src="img/tiki/Tiki_WCG.png">
						</div>
						<div id="sitetitles" style="float: left;">
							<div id="sitetitle" style="font-size: 42px;">$title</div>
						</div>
					</div>
					</header>
				</div>
			</div>
		</div>
		<div class="middle_outer">
			<div id="middle" class="fixedwidth">
				<div id="tiki-top" class="clearfix">
					<h1 style="font-size: 30px; line-height: 30px; color: #fff; text-shadow: 3px 2px 0 #781437; margin: 8px 0 0 10px; padding: 0;">
					</h1>
				</div>
			</div>
			<div id="middle" style="width: 990px;">
				$content
			</div>
		</div>
	</div><!--
	<footer id="footer" class="footer" style="margin-top: 50px;">
	<div class="footer_liner">
		<div class="footerbgtrap fixedwidth" style="padding: 10px 0;">
			<a href="http://tiki.org" target="_blank" title="Powered by Tiki Wiki CMS Groupware"><img src="img/tiki/tikibutton.png" alt="Powered by Tiki Wiki CMS Groupware" /></a>
		</div>
	</div>
</footer>-->
</div>
	</body>
</html>
END;
	die;
}
