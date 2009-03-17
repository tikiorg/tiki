<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

if (preg_match('/^adodb$/i', $api_tiki)) {
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
	$title=tra('Tiki is unable to connect to the database !');
	$content =	"		<p>".tra("The following error message was returned:")."</p>\n" .
				"		<strong>\n";
	$content .= '			'.$dbTiki->ErrorMsg();
	$content .= "		</strong>\n" .
				"		<div class=\"wikitext\" style=\"border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 30%;\">\n" .
				"			<p>".tra("Things to check:")."</p>\n" .
				"			<ol class=\"fancylist\">\n" .
				"				<li><p>".tra("Is your database up and running?")."</p></li>\n" .
				"				<li><p>".tra("Are your database login credentials correct?")."</p></li>\n" .
				"				<li><p>".tra("Did you complete the <a href='tiki-install.php' >Tiki Installer?")."</a></p></li>\n" .
				"			</ol>\n" .
				"		</div>\n" .
				"		<p>".tra("Please see <a href=\"http://doc.tikiwiki.org/\">the documentation</a> for more information.")."</p>\n";
	$dberror = true;
	include_once('tiki-install.php');
}

if (!@$dbTiki->Execute('select `login` from `users_users` limit 1')) {
	$title=tra('Tiki was unable to retrieve login data from the database !');
	$content =	"		<p>".tra("The following error message was returned:")."</p>\n" .
				"		<strong>\n";
	$content .= '			'.$dbTiki->ErrorMsg();
	$content .= "		</strong>\n" .
				"		<div class=\"wikitext\" style=\"border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 30%;\">\n" .
				"			<p>".tra("Things to check:")."</p>\n" .
				"			<ol class=\"fancylist\">\n" .
				"				<li><p>".tra("Are your database login credentials correct?")."</p></li>\n" .
				"				<li><p>".tra("Did you complete the <a href='tiki-install.php' >Tiki Installer?")."</a></p></li>\n" .
				"			</ol>\n" .
				"		</div>\n" .
				"		<p>".tra("Please see <a href=\"http://doc.tikiwiki.org/\">the documentation</a> for more information.")."</p>\n";
	$dberror = true;
	include_once('tiki-install.php');
}

if ($db_tiki == 'sybase') {
	$dbTiki->Execute('set quoted_identifier on');
}

function close_connection() {
	global $dbTiki;
	$dbTiki->Close();
}
