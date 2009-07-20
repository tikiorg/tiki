<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

switch ($db_tiki) {
	case 'postgres7':
	case 'postgres8':
		$db_tiki = 'pgsql';
		break;
	case 'mysqli':
		$db_tiki = 'mysql';
		break;
	case 'oracle':
		$db_tiki = 'oci';
}

if ($db_tiki == 'sybase') {
	// avoid database change messages
	ini_set('sybct.min_server_severity', '11');
}

try {
	$dbTiki = new PDO("$db_tiki:host=$host_tiki;dbname=$dbs_tiki", $user_tiki, $pass_tiki);
} catch (PDOException $e) {
	$title=tra('Tiki is unable to connect to the database !');
	$content =  "   <p>".tra("The following error message was returned:")."</p>\n" .
		"   <strong>\n";
	$content .= '     '.$e->getMessage();
	$content .= "   </strong>\n" .
		"   <div class=\"wikitext\" style=\"border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 30%;\">\n" .
		"     <p>".tra("Things to check:")."</p>\n" .
		"     <ol class=\"fancylist\">\n" .
		"       <li><p>".tra("Is your database up and running?")."</p></li>\n" .
		"       <li><p>".tra("Are your database login credentials correct?")."</p></li>\n" .
		"       <li><p>".tra("Did you complete the <a href='tiki-install.php' >Tiki Installer?")."</a></p></li>\n" .
		"     </ol>\n" .
		"   </div>\n" .
		"   <p>".tra("Please see <a href=\"http://doc.tikiwiki.org/\">the documentation</a> for more information.")."</p>\n";
	$dberror = true;
	include_once('tiki-install.php');
	die();
}

$dbTiki->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
$dbTiki->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
$dbTiki->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);

$pq = $dbTiki->prepare('select `login` from `users_users` limit 1');
$result = $pq->execute();
$pq->closeCursor();

if ( $result === FALSE ) {
	$title=tra('Tiki was unable to retrieve login data from the database !');
	$content =  "   <p>".tra("The following error message was returned:")."</p>\n" .
		"   <strong>\n";
	$errors = $dbTiki->errorInfo();
	$content .= '     '.$errors[2];
	$content .= "   </strong>\n" .
		"   <div class=\"wikitext\" style=\"border: solid 1px #ccc; margin: 1em auto; padding: 1em; text-align: left; width: 30%;\">\n" .
		"     <p>".tra("Things to check:")."</p>\n" .
		"     <ol class=\"fancylist\">\n" .
		"       <li><p>".tra("Are your database login credentials correct?")."</p></li>\n" .
		"       <li><p>".tra("Did you complete the <a href='tiki-install.php' >Tiki Installer?")."</a></p></li>\n" .
		"     </ol>\n" .
		"   </div>\n" .
		"   <p>".tra("Please see <a href=\"http://doc.tikiwiki.org/\">the documentation</a> for more information.")."</p>\n";
	$dberror = true;
	include_once('tiki-install.php');
}

if ($db_tiki == 'sybase') {
	$dbTiki->exec('set quoted_identifier on');
}

function close_connection() {
	global $dbTiki;
	$dbTiki= NULL;
}

require_once 'lib/core/lib/TikiDb/Pdo.php';
TikiDb::set( new TikiDb_Pdo( $dbTiki ) );

