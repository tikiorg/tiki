<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

//$api_tiki        = 'pear';
$api_tiki       = 'adodb';
$db_tiki     = 'mysql';
$dbversion_tiki = '1.8';
$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';
$dbs_tiki    = 'tiki';
$tikidomain  = '';

/*
CVS Developers: Do not change any of the above.
Instead, create a file, called local.php, containing any of
the variables listed above that are different for your 
development environment.  This will protect you from 
accidentally committing your username/password to CVS!

For example:

cd <tiki_dir>/db
cat >local.php <<EOF
<?php
\$api_tiki        = 'pear';
\$db_tiki     = 'mysql';
\$dbversion_tiki = 'mysql3';
\$host_tiki   = 'myhost';
\$user_tiki   = 'myuser';
\$pass_tiki   = 'mypass';
\$dbs_tiki    = 'mytiki';
?>
EOF

Site admins: you can use multiple virtualhosts with the same documentroot
just changing database and $tikidomain that is used to split cache directories
(template_c and modules/cache). For example a local.php file :

$host_tiki   = 'localhost';
$user_tiki   = 'root';
$pass_tiki   = '';

$host_map = array(
	'feu.org' = array(
		'dbs_tiki'    => 'tiki_cvs',
		'tikidomain'  => 'feu',
	),
	
	'localis.org' = array(
		'dbs_tiki'    => 'tiki_localis',
		'tikidomain'  => 'localis',
	),
);

if (isset($host_map[$HTTP_HOST])) {
	extract($host_map[$HTTP_HOST]);
}

*/
$file = dirname(__FILE__). '/local.php';

if (file_exists($file)) {
	require_once ($file);
}

if (preg_match('/^adodb$/i', $api_tiki)) {
	TikiInit::prependIncludePath('lib/adodb');
	TikiInit::prependIncludePath('lib/pear');
	#error_reporting (E_ALL);       # show any error messages triggered
	define('ADODB_FORCE_NULLS', 1);
	define('ADODB_ASSOC_CASE', 2);
	define('ADODB_CASE_ASSOC', 2); // typo in adodb's driver for sybase?
	include_once ('adodb.inc.php');
	if( !empty( $error_handler_file ) && is_file( $error_handler_file ) ) {
		include_once($error_handler_file);
	} else {
		include_once ('adodb-pear.inc.php');
	}
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

if (!$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
	print "
<html><body>
<p>Unable to login to the MySQL database '$dbs_tiki' on '$host_tiki' as user '$user_tiki'<br />
<a href='tiki-install.php'>Go here to begin the installation process</a>, if you haven't done so already.</p>
</body></html>
";

	print $dbTiki->ErrorMsg();
	exit;
}

if ($db_tiki == 'sybase') {
	$dbTiki->Execute("set quoted_identifier on");
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

?>
