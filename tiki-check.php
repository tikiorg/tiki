<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (file_exists('./db/local.php') && file_exists('./templates/tiki-check.tpl')) {
	$standalone = false;
	require_once ('tiki-setup.php');
	$access->check_permission('tiki_p_admin');
} else {
	$standalone = true;

	function tra($string) {
		return $string;
	}

	function render_table($var) {  
		if(is_array($var)) {              
			echo '<table style="border:2px solid grey;">';  
			foreach($var as $key => $value) {  
				echo '<tr style="border:1px solid">',  
				'<td style="border:1px black;padding:5px;">',  
				$key,  
				"</td>";
				foreach($var[$key] as $key2 => $value2) {
					echo '<td style="border:1px solid"><span style="color:';
					switch($value2) {
						case good:
							echo 'green';
							break;
						case ugly:
							echo 'yellow';
							break;
						case bad:
							echo 'red';
							break;
					}
				echo '">'.$value2.'</span></td>';
				}
				echo "</tr>";               
			}
			echo '</table>';  
		} else {  
			echo 'Nothing to display.';  
		}
	} 
}

// Get PHP properties and check them
$php_properties = array();

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
if ( $standalone ) {
	if ( empty($_POST["dbuser"]) && empty($_POST["dbpass"]) && !($php_properties['DB Driver']['setting'] == 'Not available') ) {
			print <<<DBC
			<h2>Database credentials</h2>
			Couldn't connect to database, please provide valid credentials.
			<form method="post" action="{$_SERVER['REQUEST_URI']}">
				<p><label for="dbuser">Database username</label>: <input type="text" id="dbuser" name="dbuser" /></p>
				<p><label for="dbpass">Database password</label>: <input type="password" id="dbpass" name="dbpass" /></p>
				<p><input type="submit" value=" Connect " /></p>
			</form>
DBC;
	} else {
		switch ($php_properties['DB Driver']['setting']) {
			case "PDO":
				$connection = new PDO('mysql:host=localhost;dbname=test', $_POST["dbuser"], $_POST['dbpass']);
				function query($query, $connection) {
					$result = $connection->query($query);
					$return = $result->fetchAll(PDO::FETCH_ASSOC);
					return($return);
				}
				break;
			case "MySQLi":
				$connection = new mysqli('localhost', $_POST["dbuser"], $_POST['dbpass']);
				function query($query, $connection) {
					$result = $connection->query($query);
					while (	$row = $result->fetch_assoc() ) {
						$return[] = $row;
					}
					return($return);
				}
				break;
			case "MySQL":
				$connection = mysql_connect('localhost', $_POST["dbuser"], $_POST['dbpass']);
				function query($query, $connection = '') {
					$result = mysql_query($query);
					while (	$row = mysql_fetch_array($result) ) {
						$return[] = $row;
					}
					return($return);
				}
				break;
		}
	}
} else {
	function query($query) {
		global $tikilib;
		$result = $tikilib->query($query);
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

$server_information['Web Server'] = array(
	'value' => $_SERVER['SERVER_SOFTWARE']
);

$server_information['Server Signature'] = array(
	'value' => $_SERVER['SERVER_SIGNATURE']
);

// Free disk space
$bytes = disk_free_space('.');
$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
$base = 1024;
$class = min((int)log($bytes , $base) , count($si_prefix) - 1);
$free_space =  sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class];
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
		'message' => tra('You have less than 250 megs of free disk space. This is quite tight. Tiki needs disk space for compiling templates and for uploading files.').' '.tra('When the disk runs full you will not be able to log into your Tiki any more.')
	);
} else {
	$server_properties['Disk Space'] = array(
		'fitness' => 'good',
		'value' => $free_space,
		'message' => tra('You have more than 251 megs of free disk space. Tiki will run nicely, but you may run into issues when your site grows (e.g. file uploads)').' '.tra('When the disk runs full you will not be able to log into your Tiki any more.')
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

// magic_quotes_gpc
$s = ini_get('magic_quotes_gpc');
if ($s) {
	$php_properties['magic_quotes_gpc'] = array(
		'fitness' => tra('bad'),
		'setting' => 'On',
		'message' => tra('magic_quotes_gpc is deprecated and should be off by default. See the PHP manual for details. You may experience weird behaviour of your Tiki.')
	);
} else {
	$php_properties['magic_quotes_gpc'] = array(
		'fitness' => tra('good'),
		'setting' => 'Off',
		'message' => tra('Well set! And you are future proof also as magic_quotes_gpc is deprecated.')
	);
}

// default_charset
$s = ini_get('default_charset');
if ($s) {
	$php_properties['default_charset'] = array(
		'fitness' => tra('bad'),
		'setting' => $s,
		'message' => tra('default_charset should be UTF-8. Please check your php.ini.')
	);
} else {
	$php_properties['default_charset'] = array(
		'fitness' => tra('good'),
		'setting' => $s,
		'message' => tra('Well set! Tiki is fully UTF-8 and so should your installation be.')
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
if ( $s >= 45 ) {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('good'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('This is known to behave well even for bigger sites.')
	);
} elseif ( $s < 45 && $s >= 30 ) {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('ugly'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('This will normally work, but you might run into problems when your site grows.')
	);
} else {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('bad'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('This is known to cause issues! You should raise your max_execution_time to at least 30 seconds.')
	);
}

// max_input_time
$s = ini_get('max_input_time');
if ( $s >= 50 && $s <= 90  ) {
	$php_properties['max_input_time'] = array(
		'fitness' => tra('good'),
		'setting' => $s.'s',
		'message' => tra('Your max_input_time is at').' '.$s.'. '.tra('This is a good value for production sites. If you experience timeouts (such as when performing admin functions) you may need to increase this nevertheless.')
	);
} elseif ( $s == -1 ) {
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

// max_execution_time
$s = ini_get('max_execution_time');
if ( $s >= 30 && $s <= 90  ) {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('good'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('This is a good value for production sites. If you experience timeouts (such as when performing admin functions) you may need to increase this nevertheless.')
	);
} elseif ( $s == -1 ) {
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
		'message' => tra('The fileinfo extension is needed for correct handling of uploaded files.')
	);
} else {
	$php_properties['fileinfo'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('The fileinfo extension is needed for correct handling of uploaded files.')
	);
}

// GD
$s = extension_loaded('gd');
if ((extension_loaded('gd') && function_exists('gd_info'))) {
	$gd_info = gd_info();
	$im = @imagecreate(110, 20);
	if ($im) {
		$php_properties['gd'] = array(
			'fitness' => tra('good'),
			'setting' => $gd_info['GD Version'],
			'message' => tra('The GD extension is needed for manipulation of images, e.g. also for CAPTCHAs.')
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
		'message' => tra('libxml extension is needed by Tiki.')
	);
} else {
	$php_properties['libxml'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('libxml extension is needed by Tiki.')
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
		'fitness' => tra('ugly'),
		'setting' => 'Not available',
		'message' => tra('You will not be able to connect your Tiki to an LDAP server as the needed PHP extension is missing. More info at: http://doc.tiki.org/LDAP')
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

// Check error reporting level
// TODO: Doesn't work.... always returns the same error reporting level....?!?
$s = error_reporting();
if ( $s == 0 || ini_get('display_errors') == 0 ) {
	$php_properties['error_reporting'] = array(
		'fitness' => tra('bad'),
		'setting' => $s,
		'message' => tra('You will get no errors reported. This might be the right thing for a production site, but in case of problems check your settings for display_errors and error_reporting in php.ini to get more information.')
	);
} elseif ( $s > 0 && $s < 32767 ) {
	$php_properties['error_reporting'] = array(
		'fitness' => tra('ugly'),
		'setting' => $s,
		'message' => tra('You will not get all errors reported. This is not necessarily a bad thing (and it might be just right for production sites) as you will still get critical errors reported, but sometimes it can be handy to get more information. Check your error_reporting level in php.ini in case of having issues.')
	);
} else {
	$php_properties['error_reporting'] = array(
		'fitness' => tra('good'),
		'setting' => $s,
		'message' => tra('You will get all errors reported. Way to go in case of problems as the error reports usually contain some valuable hints!') 
	);
}


// Check if ini_set works
// This has to be after checking the error_reporting level or the error_reporting
// level will always be 0 because of the ini_set in the following test
$s = ini_set('error_reporting', 'E_ALL') ;
if(empty($s) || (!$s))
{
	$php_properties['ini_set'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'Disabled',
		'message' => tra('ini_set is used in some places to accomodate for special needs of some features.')
	);
} else {
	$php_properties['ini_set'] = array(
		'fitness' => tra('good'),
		'setting' => 'Enabled',
		'message' => tra('ini_set is used in some places to accomodate for special needs of some features. Check disable_features in your php.ini.')
	);
	
}

// Get MySQL properties and check them
if ($connection || !$standalone) {
	$mysql_properties = array();

	// MySQL version
	$query = "SELECT VERSION();";
	$result = query($query, $connection);
	$mysql_version = $result[0]['VERSION()'];
	$s = version_compare($mysql_version, "5.0.2", ">=" );
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
	$query = "SHOW VARIABLES where Variable_name='max_allowed_packet'";
	$result = query($query, $connection);
	$s = $result[0]['Value'];
	$max_allowed_packet = $s / 1024 / 1024;
	if ($s >= 8 * 1024 * 1024) {
		$mysql_properties['max_allowed_packet'] = array(
			'fitness' => tra('good'),
			'setting' => $max_allowed_packet.'M',
			'message' => tra('Your max_allowed_packet setting is at').' '.$max_allowed_packet.'M. '.tra('You can upload quite big files, but keep in mind to set your script timeouts accordingly.')
		);
	} else {
		$mysql_properties['max_allowed_packet'] = array(
			'fitness' => tra('ugly'),
			'setting' => $max_allowed_packet.'M',
			'message' => tra('Your max_allowed_packet setting is at').' '.$max_allowed_packet.'M. '.tra('Nothing wrong with that, but some users might want to upload something bigger.')
		);
	}
}

if ($standalone) {
echo '<h1>Tiki Server Compatibility</h1>';
echo '<h2>MySQL Database Properties</h2>';
render_table($mysql_properties);
echo '<h2>Server Information</h2>';
render_table($server_information);
echo '<h2>Server Properties</h2>';
render_table($server_properties);
echo '<h2>PHP scripting language properties</h2>';
render_table($php_properties);
} else {
	$smarty-> assign_by_ref('server_information', $server_information);
	$smarty->assign_by_ref('server_properties', $server_properties);
	$smarty->assign_by_ref('mysql_properties', $mysql_properties);
	$smarty->assign_by_ref('php_properties', $php_properties);
	// disallow robots to index page:
	$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
	$smarty->assign('mid', 'tiki-check.tpl');
	$smarty->display("tiki.tpl");
}

