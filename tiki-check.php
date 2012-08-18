<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_security.php 40813 2012-04-07 17:32:37Z jonnybradley $

require_once ('tiki-setup.php');
$access->check_permission('tiki_p_admin');

// Basic Server environment
$server_properties['Operating System'] = array(
	'value' => PHP_OS,
);

$server_properties['Web Server'] = array(
	'value' => $_SERVER['SERVER_SOFTWARE']
);

$server_properties['Server Signature'] = array(
	'value' => $_SERVER['SERVER_SIGNATURE']
);

// Get PHP properties and check them
$php_properties = array();

// PHP Version
if (version_compare(PHP_VERSION, '5.2.0', '<')) {
	$php_properties['PHP version'] = array(
		'fitness' => 'bad',
		'setting' => phpversion(),
		'message' => 'PHP 5.2 is required!'
	);
} else {
	$php_properties['PHP version'] = array(
		'fitness' => tra('good'),
		'setting' => phpversion(),
		'message' => 'Your PHP version is recent enough.'
	);
}

// memory_limit
$memory_limit = ini_get('memory_limit');
$s = trim($memory_limit);
$last = strtolower($s{strlen($s)-1});
switch ( $last ) {
	// The following was borrowed from tiki-installer.php
	// Is that correct ?!?
	// Doesn't it always just multiply by 1024 ?!
	// The 'G' modifier is available since PHP 5.1.0
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
} elseif ( $s < 160 * 1024 * 1024 && $s > 100 * 1024 * 1024 ) {
	$php_properties['memory_limit'] = array(
		'fitness' => tra('ugly') ,
		'setting' => $memory_limit,
		'message' => tra('Your memory_limit is at').' '.$memory_limit.'. '.tra('This will normally work, but you might run into problems when your site grows.')
	);
} else {
	$php_properties['memory_limit'] = array(
		'fitness' => tra('bad'),
		'setting' => $memory_limit,
		'message' => tra('Your memory_limit is at').' '.$memory_limit.'. '.tra('This is known to cause issues! You should raise your memory_limit to at least 128M.')
	);
}

// register globals
$s = ini_get('register_globals');
if ($s) {
	$php_properties['register_globals'] = array(
		'fitness' => tra('bad'),
		'setting' => 'On',
		'message' => tra('register_globals should be off by default. See the php manual for details.')
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
		'message' => tra('magic_quotes_gpc should be off by default. See the php manual for details.')
	);
} else {
	$php_properties['magic_quotes_gpc'] = array(
		'fitness' => tra('good'),
		'setting' => 'Off',
		'message' => tra('Well set!')
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
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('This is known to cause issues! You should raise your max_execution_time to at least 30s.')
	);
}

// max_input_time
$s = ini_get('max_input_time');
if ( $s >= 50 && $s <= 90  ) {
	$php_properties['max_input_time'] = array(
		'fitness' => tra('good'),
		'setting' => $s.'s',
		'message' => tra('Your max_input_time is at').' '.$s.'. '.tra('This is a good value for production sites. If you experience timeouts (such as when performing Admin functions) you may need to increase this nevertheless.')
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
		'message' => tra('Your max_input_time is at').' '.$s.'. '.tra('It is likely that some scripts, e.g. Admin functions will not finish in this time! You should raise your max_input_time to at least 30s.')
	);
}

// max_execution_time
$s = ini_get('max_execution_time');
if ( $s >= 30 && $s <= 90  ) {
	$php_properties['max_execution_time'] = array(
		'fitness' => tra('good'),
		'setting' => $s.'s',
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('This is a good value for production sites. If you experience timeouts (such as when performing Admin functions) you may need to increase this nevertheless.')
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
		'message' => tra('Your max_execution_time is at').' '.$s.'. '.tra('It is likely that some scripts, e.g. Admin functions will not finish in this time! You should raise your max_execution_time to at least 30s.')
	);
}

// upload_max_filesize
$upload_max_filesize = ini_get('upload_max_filesize');
$s = trim($upload_max_filesize);
$last = strtolower($s{strlen($s)-1});
switch ( $last ) {
	// The 'G' modifier is available since PHP 5.1.0
	// The following was borrowed from tiki-installer.php
	// Is that correct ?!?
	// Doesn't it always just multiply by 1024 ?!
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
	// The 'G' modifier is available since PHP 5.1.0
	// The following was borrowed from tiki-installer.php
	// Is that correct ?!?
	// Doesn't it always just multiply by 1024 ?!
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

// PDO
$s = extension_loaded('pdo');
if ($s) {
	$php_properties['pdo'] = array(
		'fitness' => tra('good'),
		'setting' => 'Loaded',
		'message' => tra('The PDO extension is the suggested database abstraction layer.')
	);
} else {
	$php_properties['pdo'] = array(
		'fitness' => tra('ugly'),
		'setting' => 'Not available',
		'message' => tra('The PDO extension is the suggested database abstraction layer.').' '.tra('You can fall back to AdoDB though.')
	);
}

// mbstring
$s = extension_loaded('mbstring');
if ($s) {
	$i = ini_get('mbstring.func_overload'); 
	if ($i == 0) {
		$php_properties['mbstring'] = array(
			'fitness' => tra('good'),
			'setting' => 'Loaded',
			'message' => tra('The mbstring extension is needed for an UTF-8 compatible lower case filter in the Admin search for example.')
		);
	} else {
		$php_properties['mbstring'] = array(
			'fitness' => tra('ugly'),
			'setting' => 'Badly configured',
			'message' => tra('The mbstring extension is loaded, but mbstring.func_overload = '.' '.$i.'.'.' '.'Tiki only works with mbsring.func_overload = 0. Please check your php.ini.')
		);
	}
} else {
	$php_properties['mbstring'] = array(
		'fitness' => tra('bad'),
		'setting' => 'Not available',
		'message' => tra('The mbstring extension is needed for an UTF-8 compatible lower case filter.')
	);
}

// Check if ini_set works
$s = ini_set('error_reporting', 'ALL') ;
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
$mysql_properties = array();

// MySQL version
$mysql_version = mysql_get_server_info();
$s = substr_compare($mysql_version, '5.', 0, 2);
if ( $s == 0 ) {
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
$result = $tikilib->query($query);
$row = $result->fetchRow();
$s = $row['Value'];
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

$smarty->assign_by_ref('server_properties', $server_properties);
$smarty->assign_by_ref('mysql_properties', $mysql_properties);
$smarty->assign_by_ref('php_properties', $php_properties);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-check.tpl');
$smarty->display("tiki.tpl");
