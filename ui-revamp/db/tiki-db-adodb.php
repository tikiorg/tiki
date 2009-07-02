<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

if (preg_match('/^adodb$/i', $api_tiki)) {
	TikiInit::prependIncludePath('lib/adodb');
	if (strpos(ini_get('include_path'),'lib/pear') !== 0) 
		TikiInit::prependIncludePath('lib/pear');

	define('ADODB_FORCE_NULLS', 1);
	define('ADODB_ASSOC_CASE', 2);
	define('ADODB_CASE_ASSOC', 2); // typo in adodb's driver for sybase?
	require_once ('lib/adodb/adodb.inc.php');
	include_once ('lib/adodb/adodb-pear.inc.php');

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
	include_once ('lib/pear/DB.php');
}

$dsn = "$db_tiki://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = &ADONewConnection($db_tiki);

if (!@$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
	print '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Error: Unable to connect to the database !</title>
	<link rel="stylesheet" href="styles/tikineat.css" type="text/css">
</head>
<body >
	<div id="tiki-main">
		<div id="tiki-mid">
			<div style="margin:10px 30px;">
				<h1>
					<font color="red">Tikiwiki is unable to connect to the database.</font>
					<a title="help" href="http://doc.tikiwiki.org/Installation" target="help"><img border="0" src="img/icons/help.gif" alt="Help" /></a>
				</h1>
				<p>The following error message was returned:
					<div class="simplebox">';
	print $dbTiki->ErrorMsg();
	print '</div>
				</p>
				<p>Things to check:
					<ul>
						<li>Is your database up and running?</li>
						<li>Are your database login credentials correct?</li>
						<li>Did you complete the Tiki Installer?</li>
					</ul>
				</p>
				<p>Please see <a href="http://doc.tikiwiki.org/">the documentation</a> for more information.</p>
			</div>
		</div>
		<hr>
		<p align="center">
			<a href="http://www.tikiwiki.org" title="Tikiwiki">
			<img src="img/tiki/tikibutton2.png" alt="Tikiwiki" border="0" height="31" width="80">
			</a>
		</p>
	</div>
</body>
</html>
';
	exit;
}

if (!@$dbTiki->Execute('select `login` from `users_users` limit 1')) {
	print '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Error: Unable to retrieve login from the database !</title>
	<link rel="stylesheet" href="styles/tikineat.css" type="text/css">
</head>
<body >
	<div id="tiki-main">
		<div id="tiki-mid">
			<div style="margin:10px 30px;">
				<h1>
					<font color="red">Tikiwiki is unable to retrieve login data from the database.</font>
					<a title="help" href="http://doc.tikiwiki.org/Installation" target="help"><img border="0" src="img/icons/help.gif" alt="Help" /></a>
				</h1>
				<p>The following error message was returned:
					<div class="simplebox">';
	print $dbTiki->ErrorMsg();
	print '</div>
				</p>
				<p>Things to check:
					<ul>
						<li>Are your database login credentials correct?</li>
						<li>Did you complete the Tiki Installer?</li>
					</ul>
				</p>
				<p>Please see <a href="http://doc.tikiwiki.org/">the documentation</a> for more information.</p>
			</div>
		</div>
		<hr>
		<p align="center">
			<a href="http://www.tikiwiki.org" title="Tikiwiki">
			<img src="img/tiki/tikibutton2.png" alt="Tikiwiki" border="0" height="31" width="80">
			</a>
		</p>
	</div>
</body>
</html>
';
	exit;
}

if ($db_tiki == 'sybase') {
	$dbTiki->Execute('set quoted_identifier on');
}

function close_connection() {
	global $dbTiki;
	$dbTiki->Close();
}
