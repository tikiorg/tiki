<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

// TODO : Create sane 3rd mode for Monitoring Software like Nagios, Icinga, Shinken
// * needs authentication, if not standalone
isset($_REQUEST['nagios']) ? $nagios = true : $nagios = false;
file_exists('tiki-check.php.lock') ? $locked = true : $locked = false;
$font = 'lib/captcha/DejaVuSansMono.ttf';

if (file_exists('./db/local.php') && file_exists('./templates/tiki-check.tpl')) {
	$standalone = false;
	require_once ('tiki-setup.php');
	// TODO : Proper authentication
	if (!$nagios) {
		$access->check_permission('tiki_p_admin');
	}
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
if ( $standalone && !$locked ) {
	if ( empty($_POST['dbhost']) && !($php_properties['DB Driver']['setting'] == 'Not available') ) {
			$render .= <<<DBC
<h2>Database credentials</h2>
Couldn't connect to database, please provide valid credentials.
<form method="post" action="{$_SERVER['REQUEST_URI']}">
	<p><label for="dbhost">Database host</label>: <input type="text" id="dbhost" name="dbhost" value="localhost" /></p>
	<p><label for="dbuser">Database username</label>: <input type="text" id="dbuser" name="dbuser" /></p>
	<p><label for="dbpass">Database password</label>: <input type="password" id="dbpass" name="dbpass" /></p>
	<p><input type="submit" class="btn btn-default btn-sm" value=" Connect " /></p>
</form>
DBC;
	} else {
		try {
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
		} catch(Exception $e) {
			$render .= 'Cannot connect to MySQL. Error: '.$e->getMessage();
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
		# Check for FreeType fails without a font, i.e. standalone mode
		# Using a URL as font source doesn't work on all PHP installs
		# So let's try to gracefully fall back to some locally installed font at least on Linux
		if (!file_exists($font)) {
			$font = exec('find /usr/share/fonts/ -type f -name "*.ttf" | head -n 1', $output);
		}
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
	$bytes = @disk_free_space('.');	// this can fail on 32 bit systems with lots of disc space so suppress the possible warning
	$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
	$base = 1024;
	$class = min((int) log($bytes, $base), count($si_prefix) - 1);
	$free_space = sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class];
	if ( $bytes === false ) {
		$server_properties['Disk Space'] = array(
			'fitness' => 'ugly',
			'setting' => tra('Unable to detect'),
			'message' => tra('Cannot determine the size of this disk drive.')
		);
	} else if ( $bytes < 200 * 1024 * 1024 ) {
		$server_properties['Disk Space'] = array(
			'fitness' => 'bad',
			'setting' => $free_space,
			'message' => tra('You have less than 200 megs of free disk space. Tiki will not fit on this disk drive.')
		);
	} elseif ( $bytes < 250 * 1024 * 1024 ) {
		$server_properties['Disk Space'] = array(
			'fitness' => 'ugly',
			'setting' => $free_space,
			'message' => tra('You have less than 250 megs of free disk space. This is quite tight. Tiki needs disk space for compiling templates and for uploading files.').' '.tra('When the disk runs full you will not be able to log into your Tiki any more.').' '.tra('We can not reliably check for quotas, so be warned that if your server makes use of them you might have less disk space available.')
		);
	} else {
		$server_properties['Disk Space'] = array(
			'fitness' => 'good',
			'setting' => $free_space,
			'message' => tra('You have more than 251 megs of free disk space. Tiki will run nicely, but you may run into issues when your site grows (e.g. file uploads)').' '.tra('When the disk runs full you will not be able to log into your Tiki any more.').' '.tra('We can not reliably check for quotas, so be warned that if your server makes use of them you might have less disk space available.')
		);
	}
} else {
		$server_properties['Disk Space'] = array(
			'fitness' => 'N/A',
			'setting' => 'N/A',
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
		'fitness' => tra('bad'),
		'setting' => phpversion(),
		'message' => 'You have a quite old version of PHP. You can run Tiki 6.x LTS but not later versions.'
	);
} elseif (version_compare(PHP_VERSION, '5.3.0', '<')) {
	$php_properties['PHP version'] = array(
		'fitness' => tra('ugly'),
		'setting' => phpversion(),
		'message' => 'You have an old version of PHP. You can run Tiki 6.x LTS or 9.x LTS but not later versions.'
	);
} elseif (version_compare(PHP_VERSION, '5.5.0', '<')) {
	$php_properties['PHP version'] = array(
		'fitness' => tra('ugly'),
		'setting' => phpversion(),
		'message' => 'You have a somewhat old version of PHP. You can run Tiki 6.x LTS, 9.x LTS or 12.x LTS but not later versions.'
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
} elseif ( function_exists('opcache_get_configuration') && ( ini_get('opcache.enable') == 1 || ini_get('opcache.enable') == '1') ) {
	$php_properties['ByteCode Cache'] = array(
		'fitness' => tra('good'),
		'setting' => 'OPcache',
		'message' => tra('You are using OPcache as your ByteCode Cache which increases performance, if correctly configured. See Admin->Performance in your Tiki for more details.')
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
	if (check_isIIS()) {
		$php_properties['ByteCode Cache'] = array(
			'fitness' => tra('info'),
			'setting' => 'N/A',
			'message' => tra('You are using neither APC, WinCache nor xCache as your ByteCode Cache which would increase performance, if correctly configured. See Admin->Performance in your Tiki for more details.')
		);
	} else {
		$php_properties['ByteCode Cache'] = array(
			'fitness' => tra('info'),
			'setting' => 'N/A',
			'message' => tra('You are using neither APC, nor xCache, nor OPcache as your ByteCode Cache which would increase performance, if correctly configured. See Admin->Performance in your Tiki for more details.')
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
} elseif ( $s == -1 ) {
	$php_properties['memory_limit'] = array(
		'fitness' => tra('ugly') ,
		'setting' => $memory_limit,
		'message' => tra("Your memory_limit is unlimited. This is not necessarily bad, but it's a good idea to limit this on productions servers in order to eliminate unexpectedly greedy scripts.")
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

// session.save_handler
$s = ini_get('session.save_path');
if (empty($s) || ! is_writable($s)) {
	$php_properties['session.save_path'] = array(
		'fitness' => tra('bad'),
		'setting' => $s,
		'message' => tra('Your session.save_path must writable.')
	);
} else {
	$php_properties['session.save_path'] = array(
		'fitness' => tra('good'),
		'setting' => $s,
		'message' => tra('Your session.save_path is writable.')
	);
}

// test session work
@session_start();

if (empty($_SESSION['tiki-check'])) {
	$php_properties['session'] = array(
		'fitness' => tra('ugly'),
		'setting' => tra('empty'),
		'message' => tra('Your session is empty, try reloading the page and if you see this message again you may have a problem with your server setup.')
	);
	$_SESSION['tiki-check'] = 1;
} else {
	$php_properties['session'] = array(
		'fitness' => tra('good'),
		'setting' => 'ok',
		'message' => tra('Your appears to work.')
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
		'message' => tra('You have no time zone set! While there are a lot of fallbacks in PHP to determine the time zone, the only reliable solution is to set it explicitly in php.ini! Please check the value of date.timezone in php.ini.')
	);
} else {
	$php_properties['date.timezone'] = array(
		'fitness' => tra('good'),
		'setting' => $s,
		'message' => tra('Well done! Having a time zone set protects you from many weird errors.')
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
if ( $s >= 30 && $s <= 90 ) {
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
} else if ($s == 0) {
	$php_properties['upload_max_filesize'] = array(
		'fitness' => tra('ugly'),
		'setting' => $upload_max_filesize,
		'message' => tra('Your upload_max_filesize is at').' '.$upload_max_filesize.'. '.tra('Upload size is unlimited and this not a wise setting. A user could mistakenly upload a gigantic file which could fill up your disk. You should set this value to your realistic needs.')
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

// intl
$s = extension_loaded('intl');
if ($s) {
	$php_properties['intl'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra("intl extension is required for Tiki 15 onwards.")
	);
} else {
	$php_properties['intl'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'Not available',
		'message' => tra("intl extension is preferred for Tiki 15 onwards.")
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
		$ft = @imageftbbox(12, 0, $font, 'test');
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
		'message' => tra('This extension is needed for WebDAV and the dom extension (see below).')
	);
} else {
	$php_properties['libxml'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('This extension is needed for WebDAV and the dom extension (see below).')
	);
}

// dom (depends on libxml)
$s = extension_loaded('dom');
if ($s) {
	$php_properties['dom'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('This extension is needed for many features such as:') . '<br>' .
			tra('bigbluebutton, machine translation, SCORM & meta-data in file galleries, wiki importers, custom search, Kaltura and others.')
	);
} else {
	$php_properties['dom'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('This extension is needed for many features such as:') . '<br>' .
			tra('bigbluebutton, machine translation, SCORM & meta-data in file galleries, wiki importers, custom search, Kaltura and others.')
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

$s = extension_loaded('ssh2');
if ($s) {
	$php_properties['SSH2'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('This extension is needed for the show.tiki.org tracker field type.')
	);
} else {
	$php_properties['SSH2'] = array(
		'fitness' => tra('info'),
		'setting' => 'Not available',
		'message' => tra('This extension is needed for the show.tiki.org tracker field type.')
	);
}

$s = extension_loaded('json');
if ($s) {
	$php_properties['json'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('This extension is required for many features in Tiki.')
	);
} else {
	$php_properties['json'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('This extension is required for many features in Tiki.')
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

$s = extension_loaded('mcrypt');
$msg = tra('Enable safe, encrypted storage of data, e.g. passwords. Required for the User Encryption feature and improves encryption in other features, when available.');
if ($s) {
	$php_properties['mcrypt'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => $msg
	);
} else {
	$php_properties['mcrypt'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'Not available',
		'message' => $msg
	);
}

$s = extension_loaded('iconv');
$msg = tra('This extension is required and used frequently in validation functions invoked within Zend Framework.');
if ($s) {
	$php_properties['iconv'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => $msg
	);
} else {
	$php_properties['iconv'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => $msg
	);
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

// Xdebug
$has_xdebug = function_exists('xdebug_get_code_coverage') && is_array(xdebug_get_code_coverage());
if ($has_xdebug) {
    $php_properties['Xdebug'] = array(
        'fitness' => tra('info'),
		'setting' => 'Loaded',
        'message' => tra('Xdebug can be very handy for a development server, but it might be better to disable it when on a production server.')
    );
} else {
    $php_properties['Xdebug'] = array(
		'fitness' => tra('info'),
        'setting' => 'Not Available',
        'message' => tra('Xdebug can be very handy for a development server, but it might be better to disable it when on a production server.')
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

	// MySQL SSL
	$query = 'show variables like "have_ssl";';
	$result = query($query, $connection);
	if (empty($result)) {
		$query = 'show variables like "have_openssl";';
		$result = query($query, $connection);
	}
	$haveMySQLSSL = false;
	if (!empty($result)) {
		$ssl = $result[0]['Value'];
		$haveMySQLSSL = $ssl == 'YES';
	}
	$s = '';
	if ($haveMySQLSSL) {
		$query = 'show status like "Ssl_cipher";';
		$result = query($query, $connection);
		$isSSL = !empty($result[0]['Value']);
	} else {
		$isSSL = false;
	}
	if ($isSSL) {
		$msg = tra('MySQL SSL connection is active');
		$s = tra('ON');
	} else if($haveMySQLSSL && !$isSSL) {
		$msg = tra('MySQL connection is not encrypted');
		$s = tra('OFF');
	} else {
		$msg = tra('MySQL Server does not have SSL activated');
		$s = 'OFF';
	}
	$fitness = tra('info');
	if ($s == tra('ON')) {
		$fitness = tra('good');
	}
	$mysql_properties['SSL connection'] = array(
		'fitness' => $fitness,
		'setting' => $s,
		'message' => $msg
	);

	// Strict mode
	$query = 'SELECT @@sql_mode as Value;';
	$result = query($query, $connection);
	$s = '';
	$msg = 'Unable to query strict mode';
	if (!empty($result)) {
		$sql_mode = $result[0]['Value'];
		$modes = explode(',', $sql_mode);

		if (in_array('STRICT_ALL_TABLES', $modes)) {
			$s = 'STRICT_ALL_TABLES';
		}
		if (in_array('STRICT_TRANS_TABLES', $modes)) {
			if (!empty($s)) {
				$s .= ',';
			}
			$s .= 'STRICT_TRANS_TABLES';
		}

		if(!empty($s)) {
			$msg = 'MySQL is using strict mode';
		} else {
			$msg = 'MySQL is not using strict mode';
		}
	}
	$mysql_properties['Strict Mode'] = array(
		'fitness' => tra('info'),
		'setting' => $s,
		'message' => $msg
	);

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
				'setting' => tra('Not found'),
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
			'risky' => tra('Proc_open is similar to exec.').' '.tra('Tiki does not need it, you may want to disable it. However, Composer may need it (If you are running Tiki from SVN)'),
			'safe' =>  tra('Proc_open is similar to exec.').' '.tra('Tiki does not need it, you are wise to have it disabled. However, Composer may need it (If you are running Tiki from SVN)')
		),
		array (
			'function' => 'popen',
			'risky' => tra('popen is similar to exec.').' '.tra('Tiki needs it for file search indexing in file galleries. If you need this and trust the other PHP software on your server, you should enable it.'),
			'safe' =>  tra('popen is similar to exec.').' '.tra('Tiki needs it for file search indexing in file galleries. If you need this and trust the other PHP software on your server, you should enable it.')
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
			'risky' => tra('It is probably an urban myth that this is dangerous. Tiki team will reconsider this check, but be warned.').' '.tra('It is required for the <a href="http://doc.tiki.org/System+Configuration" target="_blank">System Configuration</a> feature.'),
			'safe' => tra('It is probably an urban myth that this is dangerous. Tiki team will reconsider this check, but be warned.').' '.tra('It is required for the <a href="http://doc.tiki.org/System+Configuration" target="_blank">System Configuration</a> feature.'),
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
		'message' => tra('allow_url_fopen may potentially be used to upload remote data or scripts. Also used by Composer to fetch dependencies. '.$feature_blogs.'If you dont use the blog feature, you can switch it off.')
	);
} else {
	$security['allow_url_fopen'] = array(
		'setting' => 'Disabled',
		'fitness' => tra('safe'),
		'message' => tra('allow_url_fopen may potentially be used to upload remote data or scripts. Also used by Composer to fetch dependencies. '.$feature_blogs.'If you dont use the blog feature, you can switch it off.')
	);
}

	// adapted from \FileGalLib::get_file_handlers
	$fh_possibilities = array(
		'application/ms-excel' => array('xls2csv %1'),
		'application/msexcel' => array('xls2csv %1'),
		// vnd.openxmlformats are handled natively in Zend
		//'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => array('xlsx2csv.py %1'),
		'application/ms-powerpoint' => array('catppt %1'),
		'application/mspowerpoint' => array('catppt %1'),
		//'application/vnd.openxmlformats-officedocument.presentationml.presentation' => array('pptx2txt.pl %1 -'),
		'application/msword' => array('catdoc %1', 'strings %1'),
		//'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => array('docx2txt.pl %1 -'),
		'application/pdf' => array('pstotext %1', 'pdftotext %1 -'),
		'application/postscript' => array('pstotext %1'),
		'application/ps' => array('pstotext %1'),
		'application/rtf' => array('catdoc %1'),
		'application/sgml' => array('col -b %1', 'strings %1'),
		'application/vnd.ms-excel' => array('xls2csv %1'),
		'application/vnd.ms-powerpoint' => array('catppt %1'),
		'application/x-msexcel' => array('xls2csv %1'),
		'application/x-pdf' => array('pstotext %1', 'pdftotext %1 -'),
		'application/x-troff-man' => array('man -l %1'),
		'application/zip' => array('unzip -l %1'),
		'text/enriched' => array('col -b %1', 'strings %1'),
		'text/html' => array('elinks -dump -no-home %1'),
		'text/richtext' => array('col -b %1', 'strings %1'),
		'text/sgml' => array('col -b %1', 'strings %1'),
		'text/tab-separated-values' => array('col -b %1', 'strings %1'),
	);

	$file_handlers = array();
	foreach ($fh_possibilities as $type => $options) {
		$file_handler = array(
			'fitness' => '',
			'message' => '',
		);
		foreach ($options as $opt) {
			$optArray = explode(' ', $opt, 2);
			$exec = reset($optArray);

			$which_exec = `which $exec`;
			if ($which_exec) {
				$file_handler['fitness'] = 'good';
				$file_handler['message'] = "will be handled by $which_exec";
				break;
			}
		}
		if (! $file_handler['fitness']) {
			$file_handler['fitness'] = 'ugly';
			$fh_commands = '';
			foreach ($options as $opt) {
				$fh_commands .= $fh_commands ? ' or ' : '';
				$fh_commands .= '"' . substr($opt, 0, strpos($opt, ' ')) . '"';
			}
			$file_handler['message'] = 'You need to install ' . $fh_commands . ' to index this type of file';
		}
		$file_handlers[$type] = $file_handler;
	}



if (!$standalone) {
	// The following is borrowed from tiki-admin_system.php
	if ($prefs['feature_forums'] == 'y') {
		$dirs = TikiLib::lib('comments')->list_directories_to_save();
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
	$dirs = array_unique($dirs);
	$dirsExist = array();
	foreach ($dirs as $i => $d) {
		$dirsWritable[$i] = is_writable($d);
	}
	$smarty->assign_by_ref('dirs', $dirs);
	$smarty->assign_by_ref('dirsWritable', $dirsWritable);

	// Prepare Monitoring acks
	$query = "SELECT `value` FROM tiki_preferences WHERE `name`='tiki_check_status'";
	$result = $tikilib->getOne($query);
	$last_state = json_decode($result, true);
	$smarty->assign_by_ref('last_state', $last_state);

	function deack_on_state_change(&$check_group, $check_group_name) {
		global $last_state;
		foreach ( $check_group as $key => $value ) {
			if (! empty($last_state["$check_group_name"]["$key"]))
			{
				$check_group["$key"]['ack'] = $last_state["$check_group_name"]["$key"]['ack'];
				if (isset($check_group["$key"]['setting']) && isset($last_state["$check_group_name"]["$key"]['setting']) &&
							$check_group["$key"]['setting'] != $last_state["$check_group_name"]["$key"]['setting']) {

					$check_group["$key"]['ack'] = false;
				}
			}
		}
	}
	deack_on_state_change($mysql_properties, 'MySQL');
	deack_on_state_change($server_properties, 'Server');
	if ($apache_properties) {
		deack_on_state_change($apache_properties, 'Apache');
	}
	if ($iis_properties) {
		deack_on_state_change($iis_properties, 'IIS');
	}
	deack_on_state_change($php_properties, 'PHP');
	deack_on_state_change($security, 'PHP Security');
}

if ($standalone && !$nagios) {
	$render .= '<style type="text/css">td, th { border: 1px solid #000000; vertical-align: baseline; padding: .5em; }</style>';
//	$render .= '<h1>Tiki Server Compatibility</h1>';
	if (!$locked) {
		$render .= '<h2>MySQL or MariaDB Database Properties</h2>';
		renderTable($mysql_properties);
		$render .= '<h2>Test sending emails</h2>';
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
			$render .= '<p><input type="submit" class="btn btn-default btn-sm" value=" Send e-mail " /></p>';
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

	$render .= '<h2>File Gallery Search Indexing</h2>';
	$render .= '<em>More info <a href="https://doc.tiki.org/Search+within+files">here</a></em>
	';
	renderTable($file_handlers);

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
} elseif ($nagios) {
//  0	OK
//  1	WARNING
//  2	CRITICAL
//  3	UNKNOWN
	$monitoring_info = array( 'state' => 0,
			 'message' => '');

	function update_overall_status($check_group, $check_group_name) {
		global $monitoring_info;
		$state = 0;
		$message = '';

		foreach ($check_group as $property => $values) {
			if ($values['ack'] != true) {
				switch($values['fitness']) {
					case 'ugly':
						$state = max($state, 1);
						$message .= "$property"."->ugly, ";
						break;
					case 'risky':
						$state = max($state, 1);
						$message .= "$property"."->risky, ";
						break;
					case 'bad':
						$state = max($state, 2);
						$message .= "$property"."->BAD, ";
						break;
					case 'info':
						$state = max($state, 3);
						$message .= "$property"."->info, ";
						break;
					case 'good':
					case 'safe':
						break;
				}
			}
		}
		$monitoring_info['state'] = max($monitoring_info['state'], $state);
		if ($state != 0) {
			$monitoring_info['message'] .= $check_group_name.": ".trim($message, ' ,')." -- ";
		}
	}

	// Might not be set, i.e. in standalone mode
	if ($mysql_properties) {
		update_overall_status($mysql_properties, "MySQL");
	}
	update_overall_status($server_properties, "Server");
	if ($apache_properties) {
		update_overall_status($apache_properties, "Apache");
	}
	if ($iis_properties) {
		update_overall_status($iis_properties, "IIS");
	}
	update_overall_status($php_properties, "PHP");
	update_overall_status($security, "PHP Security");
	$return = json_encode($monitoring_info);
	echo $return;
} else {
	if (isset($_REQUEST['acknowledge']) || empty($last_state)) {
		$tiki_check_status = array();
		function process_acks(&$check_group, $check_group_name) {
			global $tiki_check_status;
			foreach($check_group as $key => $value) {
				$formkey = str_replace(array('.',' '), '_', $key);
				if (isset($check_group["$key"]['fitness']) &&
					($check_group["$key"]['fitness'] === 'good' || $check_group["$key"]['fitness'] === 'safe') || $_REQUEST["$formkey"] === "on")
				{
					$check_group["$key"]['ack'] = true;
				} else {
					$check_group["$key"]['ack'] = false;
				}
			}
			$tiki_check_status["$check_group_name"] = $check_group;
		}
		process_acks($mysql_properties, 'MySQL');
		process_acks($server_properties, 'Server');
		if ($apache_properties) {
			process_acks($apache_properties, "Apache");
		}
		if ($iis_properties) {
			process_acks($iis_properties, "IIS");
		}
		process_acks($php_properties, "PHP");
		process_acks($security, "PHP Security");
		$json_tiki_check_status = json_encode($tiki_check_status);
		$query = "INSERT INTO tiki_preferences (`name`, `value`) values('tiki_check_status', ? ) on duplicate key update `value`=values(`value`)";
		$bindvars = array($json_tiki_check_status);
		$result = $tikilib->query($query, $bindvars);
	}
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
	$smarty->assign_by_ref('file_handlers', $file_handlers);
	// disallow robots to index page:

	$fmap = array(
		'good' => array('icon' => 'ok', 'class' => 'success'),
		'safe' => array('icon' => 'ok', 'class' => 'success'),
		'bad' => array('icon' => 'ban', 'class' => 'danger'),
		'unsafe' => array('icon' => 'ban', 'class' => 'danger'),
		'risky' => array('icon' => 'warning', 'class' => 'warning'),
		'ugly' => array('icon' => 'warning', 'class' => 'warning'),
		'info' => array('icon' => 'information', 'class' => 'info'),
		'unknown' => array('icon' => 'help', 'class' => 'muted'),
	);
	$smarty->assign('fmap', $fmap);
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
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="//dev.tiki.org/vendor/twitter/bootstrap/dist/css/bootstrap.css" />
		<title>$title</title>
		<style type="text/css">
			table { border-collapse: collapse;}
			#fixedwidth {   width: 1024px; margin: 1em auto; }
			#middle {  margin: 0 auto; }
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
						<div class="sitelogo" style="float: left">
END;
echo tikiLogo();
							echo <<< END
						</div>
						<div class="sitetitles" style="float: left;">
							<div class="sitetitle" style="font-size: 42px;">$title</div>
						</div>
					</div>
					</header>
				</div>
			</div>
		</div>
		<div class="middle_outer">
			<div id="middle" class="fixedwidth">
				<div class="topbar clearfix">
					<h1 style="font-size: 30px; line-height: 30px; color: #fff; text-shadow: 3px 2px 0 #781437; margin: 8px 0 0 10px; padding: 0;">
					</h1>
				</div>
			</div>
			<div id="middle" style="width: 990px;">
				$content
			</div>
		</div>
	</div>
	<footer id="footer" class="footer" style="margin-top: 50px;">
	<div class="footer_liner">
		<div class="footerbgtrap fixedwidth" style="padding: 10px 0;">
			<a href="http://tiki.org" target="_blank" title="Powered by Tiki Wiki CMS Groupware">
END;
echo tikiButton();
echo <<< END
				<img src="img/tiki/tikibutton.png" alt="Powered by Tiki Wiki CMS Groupware" />
			</a>
		</div>
	</div>
</footer>
</div>
	</body>
</html>
END;
	die;
}

function tikiLogo()
{
	return '<img alt="Tiki Logo" src="data:image/png;base64,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%3D" />';
}

function tikiButton ()
{
	return '<img alt="tikibutton" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFgAAAAfCAYAAABjyArgAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAC09JREFUeNrsmnlUFEcex7/TM4DggCiIxwBGPIgaBjwREGKMIiIm+jRoNrrGXX1RX7IBz5hDd5PVjb6Nxo2J8YzXJsYjicR4R8UDQzSKB4IixoByhVPua3qrfj3dziUaNn+M7pavrOrq7jo+9e1f/aoGjeegqa0AfMPiYDyiQaUyppRXGaMAtVoFtcBTARq1mkVjqpHy/J5KUEGQK2BBFEUYDCIaDQYpNhpQ39Co5Cmy+wZRyvPn70X2PvtnDMddXZzHgAE+tnTDHvF/OZRUVP/udS7buEdsGz71mMov6jUxc/+/2Gw00iw8rkFlolI57E35GW98eRK3CkohMoW+9Xww3hwd8sC6HsSJt6VmX4pXxDRo5EIDa+BxhmsJ5fLtIry07ggfOASNBiL73Jck/Ihvz2fii9di0MmzVbMh83scMA/C3YpqPM7BlnJ5eHV7EgS1Bio5aqT08p1ihC76HNtOpjarXrNn2D+NrRtXbmRj0ce7lOuosEBMGzfEbqCNm/UhYocPpNgcCMsOX0FqfrmkXKZg0cAUzk0ke9zQCNytbcD0jYeZmm9gzdThcG/Z4qG/DMvFV7B1g6v6zMUMREf0xisvDMUHm7+jaC+B9y3n15Jmv3/wWh7BJdUyj8JUwZKq1RS/u3ATIe9swYmrWc1TMrsnNNWRp7r6IDI0AL26etOgeFj48U50HDITT46ajS8PnDErs2yM3+fl5ZU1lOfv8Ou4pVvo/tj4FXQ94MW3KV3/1TGrNtbtOqrUJb8PiM02Ddw2umudFZimQOUo8FSQ8llFFYhash1ztxxBKRvHbzJPQNOAeSirqEJ2XhF8O3ji4OlLWL/7GL5eOZtMRvyyrbhbWU0mhIfUzNsKHJ7nk8InibfEn30ldiiu712BHQd/wM5Dydi9Ip7eC+jmi/zENRgfFYIDpy4qbaxcMBmLPtmFq5l3lPfzjn/afOVeL5B8ZAZPUTADrPfxJNAcfLi/jpkEPgHcRxYk6Ox5bi4i392GxNRbZmySUq7TWG2vZQ9Q8JjXP0CP5+YQ3PdejSXbzENIYDeKPPDBhwZ1lz7dlAwCRINhk8EbD+3dHVcypPeycguxZucRyt/OL773pXTzITfR1aUFdZaH0xeuYf/JFKmupEuU8nbueTuq36TeM1kl2HheqpugaSTlzo8MwIlZ0QRR79MW++JioPf1khRMUQK9YeZzWDR+MCL/ugWzN+5naq5Wvqwpb3+q9NvSBmuaAsxVJIPkA3NjMyvPnDxjrVxdFOhrd31P4EYMCiLQPB8W5A9X4yLBQXJFD+rzJHzae9hsU24jrLc/xRejw6i9/ybwRWvOoQz4tJL6IZAZYAscm6S1yT9jX1qOUamS3vjujudFDleU0jlbv0fJ3Up+Ex/tTcYPP6Vh39LpmPNyDH3h3IzaCjb9YHll5GlDQ4NSzu0xX+xe/8dmqpTD6umnI/VxwMu37KOy2KiBNKsyeJcWDvTc9n1JGB6mp9leMXeSYsO5Wgc85UfPym2s2XEEPbvoaJKWz50I73Zt8M5HOyiV9WvLd7e1srs7OyJ5eqhyvXviAKv3PN/cqQAGS8P9vZmaR5o9k5j6C55dtIkBV+FcdhEWrtqJFfMmQefV+j57CRM3ra6uTilu18YVsyePpMFYlu9dNRt7jl8gFY6LHIDqaknJo5/pQ4ML79sDgd10iJ80gu35NQSX1/H5+zNx9GwacgvLMDKiD+3Z+cTMnTKK3ndwkJ6T2zh2Nh0V1bXkyfCO8/cTElPQqWNbzJocDXfXlkrfLE2CfM3To9nlyCirp2tvrSNe6OmFHal5yC6tpk1GiG9rhHZqI5kDlQRY7+2B+cMDcSm7EPPYTo+7cAfnjzMedgjGsw7QWrIsfoLNjYeTk5O5iTAF6dVai2ljwq3KLe+hsR51jdbltbW1eGXs05SvqKigtIWjGtFhT5nUZEBMuN6sbrktXtf4yH5KeVVVFZVNHT3I6nlbcOWYW9WAN0/nMBPRyAkguIOWAO+6WoCkX4qZz9uAWWyiJcAMriDV9UZUEAGbsTURF2/lw2DyFdMXwiC3rK4k08e/cPmwxxI0z9oE/Cju0kzBytdxpwpQYRAgOAjSlti4fQUtXEyxjIBSh1GZtK4wk1JaVYuymnpppVLakfJOtVVwrK/F5LFDUV9fb3aiZg5ZtD/Ay7cdZuMXMGvisCbPR5qCy98/n1WMGxUic8ccaKB8x8ahSiyNLph4Dx5f2OT80kOXybtYPTEC0csTYPqRCMxcuJSXon+vzpg+bjApmPfT/NhStF7k7AVw2s85cHZ2JlhN9clUqZaQOeC60mLUpWWjhb4f7UtEVSPKG6SBxw3wxsCOrlh/Luueu0f1SPlTmflwO6HBjIgeWBDTF4u/SabywE5eOLLwJaC+Di3UBlKvDFcWg7mSTQDzh+0h8M5xf7v3i3+D1tkJrzHfMyzQDzHxqzH3j1GICeuB3Ucv4JNdJ3Bs7Ty2ANWZwZUBd9F5QpO2B/XubeDYuRtEBjetpBYrf8pDcPuWGKhzw47LDrhdVkP2mB/M3K2tx+mbBWQaFnx9Fj7uLgjvriPuJ9JvQ6ithsBA+ndqi8rKSivA1gq2QxPBO9pK64LTmxdh5nvrse3AOYwcFIBQfWfsP5NKgE+cz2DuXiB5KCUlFVZweerE7O5bE4fg7xv2ojzdFy4RUXyfjJXn82jRMjQw28kA7SiuwPYU6azhSm4Znl+XqCxq/DhTzo9guziX7Ezqy8JpMdRP7gU1BVi0RxPBO8c3ISVFBfDxcsPJlBvQarUY0rcrFm86guLyGly8cQdTX4hEYWEhDdISsBxDAp7AxoWTsOSzA7j4xVo4DxoGQecrfbui/OOO/L/0kw9MyhRzxNpwys1Cew83/GXCM6Rc3q78IwWHa1PBop0qmAe+ePBzDjetMw2kb/eOpNh//ls6/Onl1x5lhbk27a+c8oMdDzdnrIgfy8xKCjbtPYyaDk/AISiYPAm28kkw5AgjHFGCIxpTh8J8CEzxC14eDSeNQG7oQylYtFMF813iZ9+ewdGfbmDiqAiUlpZS/54OZGYi+TrblvrASWg022jcD7Cs5tFPB0DftSOWbj2Mmwe+gkNftrNr7Unum3QmLFIeMiBjVFWWQ1PyKyaPDEavzu2oTVm9snLv50WI9miD/xwzAIKTFr/kl+L9+JcQ0lOH9PR0ujesnx8BnjAiFDk5OTYBW5oJOXLY3OSsnheLrfvPsngIQvdeEPyeZEAMipoluEZlM9U63rkFP50H/hDZR4Frqt6mANulm+buzF2lSgR0YP5rQxEuXSqi8gsZeUhIuk4mI0zvh5vXrth00e4H2VTNE4YGIbiXL97dcBAFubehCugH0cGRlAwFlgHq/DtQMZcsLjaCzIKpck0BW5oIm4c99r6T69zeFfOnRKOLbwfkZmVabZMfRsWmoL09XfFh3PNYn5CMoz8mQuRK9mhnNBcMLltkNUX5GP9sIHSeWtTU1JiBtQRsS72PFGANY9lwNw/XruQ9cMNhaoctYcuAeapm92aMDkY/fx1WfZWESqZmg6sb1DXVUBf/ik7t3DEmvIcV3KYWuIc6TXsUzyPuZy5sQbaMAZ09sXjqUKxJOIv0LMkn9vfxQNy4EDottGVzm1zcTFK728n93gc/TUE2vad1EjAndiCq2C7O2UljBNbI1FtvE6wpXFtn6VYmIigoCOfOnXvsIZuCNi1XzAtLa2oaFJ/cciPRtN8rwtHREXq93gzw8WUbEwbP+9Nz6N+/P/4ffp/AmPLkuOpx+OvKe+oVlZ+TVMZzXFIlKVVOBcqrjalAvzIbU+NpmrxtNsh/adloNAui9FeXvMzK/pocshvDcf5Dz38EGAD34AT1F6wekAAAAABJRU5ErkJggg%3D%3D"';

}
