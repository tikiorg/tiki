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

$dbTiki = new PDO("$db_tiki:host=$host_tiki;dbname=$dbs_tiki", $user_tiki, $pass_tiki);
$dbTiki->setAttribute(PDO::ATTR_CASE,PDO::CASE_NATURAL);
$dbTiki->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_WARNING);
$dbTiki->setAttribute(PDO::ATTR_ORACLE_NULLS,PDO::NULL_EMPTY_STRING);
//$dbTiki->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,false);
if ($dbTiki->getAttribute(PDO::ATTR_DRIVER_NAME) == 'mysql') {
	$dbTiki->exec("SET CHARACTER SET utf8");
}


$pq = $dbTiki->prepare('select `login` from `users_users` limit 1');
$result = $pq->execute();
$pq->closeCursor();

if ( $result === FALSE ) {
	print '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Error: Unable to connect to the database !</title>
	<link rel="stylesheet" href="styles/thenews.css" type="text/css">
</head>
<body >
	<div id="tiki-main">
		<div id="tiki-mid">
			<div style="margin:10px 30px;">
				<h1><font color="red">TikiWiki is unable to connect to the database.</font> <a title="help" 
href="http://doc.tikiwiki.org/Installation" target="help"><img border="0" src="img/icons/help.gif" alt="Help" /></a></h1>
';
	print '<p>The following error message was returned:<div class="simplebox">';
	$errors = $dbTiki->errorInfo();
	print $errors[2];
	print '</div></p><p>Things to check:<ul><li>Is your database up and running?</li><li>Are your database login credentials correct?</li><li>Did you complete the Tiki Installer?</li></ul>
<p>Please see <a href="http://doc.tikiwiki.org/">the documentation</a> for more information.</p>
</div>
		</div>
		<hr>
		<p align="center">
			<a href="http://www.tikiwiki.org" title="TikiWiki">
  			<img src="img/tiki/tikibutton2.png" alt="TikiWiki" border="0" height="31" width="80">
			</a>
		</p>
	</div>
</body>
</html>
';
	exit;
}

if ($db_tiki == 'sybase') {
	$dbTiki->exec('set quoted_identifier on');
}

function close_connection() {
	global $dbTiki;
	$dbTiki= NULL;
}
