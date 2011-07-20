<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

	define('ADODB_FORCE_NULLS', 1);
	define('ADODB_ASSOC_CASE', 2);
	define('ADODB_CASE_ASSOC', 2); // typo in adodb's driver for sybase?
	require_once ('lib/adodb/adodb.inc.php');
	include_once ('lib/adodb/adodb-pear.inc.php');

	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

// ADODB_FETCH_BOTH appears to be buggy for null values

$dsn = "$db_tiki://$user_tiki:$pass_tiki@$host_tiki/$dbs_tiki";
//$dsn = "mysql://$user_tiki@$pass_tiki(localhost)/$dbs_tiki";
$dbTiki = ADONewConnection($db_tiki);

if (!@$dbTiki->Connect($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki)) {
	require_once 'lib/init/smarty.php';

	$smarty->assign( 'msg', $dbTiki->ErrorMsg() );
	$smarty->assign( 'where', 'connection');
	echo $smarty->fetch( 'database-connection-error.tpl' );
	exit;
}

// Set the Client Charset
if ( isset( $client_charset ) ) {
	@ $dbTiki->Execute("SET CHARACTER SET $client_charset");
}

if (!@$dbTiki->Execute('select login from users_users limit 1')) {
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

if( ! function_exists( 'close_connection' ) ) {
	function close_connection() {
		global $dbTiki;
		$dbTiki->Close();
	}
}

require_once 'lib/core/TikiDb/Adodb.php';
TikiDb::set( new TikiDb_Adodb( $dbTiki ) );
