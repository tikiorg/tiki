<?php
/* $Id$ */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

require_once('lib/init/initlib.php');

// Define and load Smarty components
define('SMARTY_DIR', "lib/smarty/libs/");
require_once ( 'lib/smarty/libs/Smarty.class.php');

// Define lang and load translation functions
if (!empty($_REQUEST['lang'])) {
	$language = $prefs['site_language'] = $prefs['language'] = $_REQUEST['lang'];
} else {
	$language = $prefs['site_language'] = $prefs['language'] = 'en';
}
include_once('lib/init/tra.php');

// Please use the local.php file instead containing these variables
// If you set sessions to store in the database, you will need a local.php file
// Otherwise you will be ok.
//$api_tiki		= 'pear';
//$api_tiki			= 'pdo';
$api_tiki			= 'adodb';
$db_tiki			= 'mysql';
$dbversion_tiki = '2.0';
$host_tiki		= 'localhost';
$user_tiki		= 'root';
$pass_tiki		= '';
$dbs_tiki			= 'tiki';
$tikidomain		= '';

/*
SVN Developers: Do not change any of the above.
Instead, create a file, called db/local.php, containing any of
the variables listed above that are different for your 
development environment.  This will protect you from 
accidentally committing your username/password to SVN!

example of db/local.php
<?php
$host_tiki   = 'myhost';
$user_tiki   = 'myuser';
$pass_tiki   = 'mypass';
$dbs_tiki    = 'mytiki';
$api_tiki    = 'adodb';
?>

** Multi-tiki
**************************************
see http://tikiwiki.org/MultiTiki19

Setup of virtual tikis is done using setup.sh script
-----------------------------------------------------------
-> Multi-tiki trick for virtualhosting

$tikidomain variable is set to :
or TIKI_VIRTUAL
    That is set in apache virtual conf : SetEnv TIKI_VIRTUAL myvirtual
or SERVER_NAME
    From apache directive ServerName set for that virtualhost block
or HTTP_HOST
    From the real domain name called in the browser 
    (can be ServerAlias from apache conf)

*/

if (!isset($local_php) or !is_file($local_php)) {
	$local_php = 'db/local.php';
} else {
	$local_php = preg_replace(array('/\.\./','/^db\//'),array('',''),$local_php);
}
if (is_file('db/virtuals.inc')) {
	if (!isset($multi)) {
		if (isset($_SERVER['TIKI_VIRTUAL']) and is_file('db/'.$_SERVER['TIKI_VIRTUAL'].'/local.php')) {
			$multi = $_SERVER['TIKI_VIRTUAL'];
		} elseif (isset($_SERVER['SERVER_NAME']) and is_file('db/'.$_SERVER['SERVER_NAME'].'/local.php')) {
			$multi = $_SERVER['SERVER_NAME'];
		} elseif (isset($_SERVER['HTTP_HOST']) and is_file('db/'.$_SERVER['HTTP_HOST'].'/local.php')) {
			$multi = $_SERVER['HTTP_HOST'];
		}
	}
	if (isset($multi)) {
		$local_php = "db/$multi/local.php";
		$tikidomain = $multi;
	}
}
$re = include($local_php);
if ( $re === FALSE) {
	print '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>'.$prefs['site_title'].': '.tra('Installation begins').': '.$local_php.tra(' not found').'</title>
	<link rel="stylesheet" href="styles/thenews.css" type="text/css">
</head>
<body >
	<div id="tiki-main">
		<div id="tiki-mid">
			<div style="margin:10px 30px;">
				<h1 style="color: red">'.tra("Tiki cannot find your $local_php file.").' <a title="help" href="http://doc.tikiwiki.org/Installation" target="help"><img border="0" src="img/icons/help.gif" alt="Help" /></a></h1>
				<p>'.tra('This is normal for a brand new Tiki installation.').'</p>
				<p>'.tra('Run the Tiki Installer ').'(<a 
href=tiki-install.php>tiki-install.php</a>)'.tra(' to create and transfer information to your db/local.php file.
This file contains the information (username/password/database name/etc.) to connect to a database.').'</p>
				<p>'.tra('Please see <a 
href="http://doc.tikiwiki.org/">the documentation</a> for more information.').'</p>
			</div>
		</div>
		<hr />
		<p style="text-align: center">
			<a href="http://tikiwiki.org" title="TikiWiki CMS/Gropuware Project">
 	 			<img src="img/tiki/tikibutton2.png" alt="TikiWiki" border="0" height="31" width="80" />
			</a>
		</p>
	</div>
</body>
</html>
';
	exit;
}

if ( $dbversion_tiki == '1.10' ) $dbversion_tiki = '2.0';

if (extension_loaded("pdo") and $api_tiki == 'pdo' ) {
	require_once('db/tiki-db-pdo.php');
} else {
	require_once('db/tiki-db-adodb.php');
}

// Forget db info so that malicious PHP may not get password etc.
$host_tiki = NULL;
$user_tiki = NULL;
$pass_tiki = NULL;
$dbs_tiki = NULL;

unset ($host_map);
unset ($db_tiki);
unset ($host_tiki);
unset ($user_tiki);
unset ($pass_tiki);
unset ($dbs_tiki);
