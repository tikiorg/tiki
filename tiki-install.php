<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-install.php,v 1.86.2.1 2007-11-04 22:08:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function installer_is_accessible()
{
	if( ! isset( $_SESSION['accessible'] ) )
		return false;

	return true;
}

if( file_exists( 'db/lock' ) )
	die( 'Installer disabled. Remove \'db/lock\' to enable the installer.' );

session_start();

if ( file_exists( 'db/local.php' ) ) {

	include('db/local.php');
	include_once('lib/adodb/adodb.inc.php');
	$dbTiki = &ADONewConnection($db_tiki);

	if( isset( $_POST['dbuser'], $_POST['dbpass'] ) )
	{
		if( $_POST['dbuser'] == $user_tiki && $_POST['dbpass'] == $pass_tiki )
			$_SESSION['accessible'] = true;
	}
}
else
	$_SESSION['accessible'] = true;

if ( installer_is_accessible() ) {
	$logged = true;
	$admin_acc = 'y';
	include_once("installer/tiki-installer.php");
} else {
?>
<html>
<body>
<h1><?php echo ('Security Alert') ?>!</h1>
<p><?php echo ( 'The Tiki installer can be used only by the site administrator. Please enter the database credentials as a verification. If you forgot about them, they are located in \'db/local.php\'') ?></p>
<form method="post" action="tiki-install.php">
<p><?php echo ( 'Database username' ) ?>: <input type="text" name="dbuser"/></p>
<p><?php echo ( 'Database password' ) ?>: <input type="text" name="dbpass"/></p>
<p><input type="submit" value="Validate"/></p>
</form>
</body>
</html>
<?php
}

?>
