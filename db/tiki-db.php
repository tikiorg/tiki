<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

//$api_tiki        = 'pear';

// Please use the local.php file instead containing these variables
// If you set sessions to store in the database, you will need a local.php file
// Otherwise you will be ok.
$api_tiki       = 'adodb';
$db_tiki     = 'mysql';
$dbversion_tiki = '1.10';
$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';
$dbs_tiki    = 'tiki';
$tikidomain  = '';

/*
CVS Developers: Do not change any of the above.
Instead, create a file, called db/local.php, containing any of
the variables listed above that are different for your 
development environment.  This will protect you from 
accidentally committing your username/password to CVS!

example of db/local.php
<?php
$host_tiki   = 'myhost';
$user_tiki   = 'myuser';
$pass_tiki   = 'mypass';
$dbs_tiki    = 'mytiki';
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
  $local_php = 'local.php';
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
		$local_php = "$multi/local.php";
		$tikidomain = $multi;
	}
}
$re  = include_once('db/'.$local_php);
if ( $re === FALSE) {
	die("<b style=\"color:red;\">$local_php not found.</b><br /><br />Please run <a href=tiki-install.php>tiki-install.php</a>");
}
if (preg_match('/^adodb$/i', $api_tiki)) {
	TikiInit::prependIncludePath('lib/adodb');
	TikiInit::prependIncludePath('lib/pear');
	#error_reporting (E_ALL);       # show any error messages triggered
	define('ADODB_FORCE_NULLS', 1);
	define('ADODB_ASSOC_CASE', 2);
	define('ADODB_CASE_ASSOC', 2); // typo in adodb's driver for sybase?
	require_once ('adodb.inc.php');
	include_once ('adodb-pear.inc.php');
	//include_once('adodb-error.inc.php');
	//include_once('adodb-errorhandler.inc.php');
	//include_once('adodb-errorpear.inc.php');

	if ($db_tiki == 'pgsql') {
		$db_tiki = 'postgres7';
	}

	if ($db_tiki == 'sybase') {
		// avoid database change messages
		ini_set('sybct.min_server_severity', '11');
	}

	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

// ADODB_FETCH_BOTH appears to be buggy for null values
} else {
	// Database connection for the tiki system
	include_once ('DB.php');
}

//doesn't work with adodb. adodb doesn't let you inherit
/*
class tikiDB extends ADOConnection {
  var $dbversion;
}
*/
$dsn = "$db_tiki://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = &ADONewConnection($db_tiki);

if (!@$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki) 
		or (!@$dbTiki->Execute('select `login` from `users_users` limit 1'))) {
	print "
<html><body>
<h1><font color='red'>Unable to connect to the database !</font></h1>
";
	print $dbTiki->ErrorMsg();
	exit;
}

if ($db_tiki == 'sybase') {
	$dbTiki->Execute('set quoted_identifier on');
}

// set db version
//$dbTiki->dbversion=$dbversion_tiki;

// Forget db info so that malicious PHP may not get password etc.
$host_tiki = NULL;
$user_tiki = NULL;
$pass_tiki = NULL;
$dbs_tiki = NULL;

unset ($host_map);
unset ($api_tiki);
unset ($db_tiki);
unset ($host_tiki);
unset ($user_tiki);
unset ($pass_tiki);
unset ($dbs_tiki);

function close_connection() {
	global $dbTiki;
	$dbTiki->Close();
}
?>
