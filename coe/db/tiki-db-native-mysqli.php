<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== false) {
  header('location: index.php');
  exit;
}

$dbTiki = new mysqli($host_tiki, $user_tiki, $pass_tiki, $dbs_tiki);

if (mysqli_connect_errno()) {
	$title=tra('Tiki is unable to connect to the database !');
	$content =  "   <p>".tra("The following error message was returned:")."</p>\n" .
		"   <strong>\n";
	$content .= '     '.mysqli_connect_error();
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

$dbTiki->set_charset('utf8');

$pq = $dbTiki->prepare('select `login` from `users_users` limit 1');
$result = $pq->execute();
$pq->close();

if ( $result === FALSE ) {
	$title=tra('Tiki was unable to retrieve login data from the database !');
	$content =  "   <p>".tra("The following error message was returned:")."</p>\n" .
		"   <strong>\n";
	$content .= '     '.$dbTiki->error;
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
	die();
}
