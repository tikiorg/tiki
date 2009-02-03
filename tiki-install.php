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
{
 echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" href="styles/tikineat.css" rel="stylesheet" />
<title>TikiWiki Installer Disabled</title>
</head>
<body class="tiki_wiki">
<div id="siteheader">
	<div id="sitelogo" style="text-align: left;">
		<img style="border: medium none ;" alt="Site Logo" src="img/tiki/tikilogo.png" />
	</div>
</div>
<div id="tiki-main">
	<div id="tiki-mid">
		<table id="tiki-midtbl" width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td id="centercolumn" valign="top">
					<h1>TikiWiki Installer Disabled</h1>
					<p>As a security precaution, the TikiWiki Installer has been disabled. To re-enable the installer:<br /><ol><li>Use your file manager application to remove the <code>&lt;INSTALL DIRECTORY&gt;\db\lock</code> file.</li><li>Re-run <strong><a href="tiki-install.php" alt="TikiWiki Installer">tiki-install.php</a></strong>.</li></ol></p>
				</td>
			</tr>
		</table>
	</div>
	<div id="tiki-bot" align="center">
		<a title="This is TikiWiki CMS/Groupware" href="http://info.tikiwiki.org" target="_blank"><img src="img/tiki/tikibutton2.png" alt="TikiWiki" border="0" /></a>
	</div>
</div>
</body>
</html>
<?php
	die;
	}
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
	echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link type="text/css" href="styles/tikineat.css" rel="stylesheet" />
<title>TikiWiki Installer Security Alert</title>
</head>
<body class="tiki_wiki">
<div id="siteheader">
	<div id="sitelogo" style="text-align: left;">
		<img style="border: medium none ;" alt="Site Logo" src="img/tiki/tikilogo.png" />
	</div>
</div>
<div id="tiki-main">
	<div id="tiki-mid">
		<table id="tiki-midtbl" width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td id="centercolumn" valign="top">
					<h1>TikiWiki Installer Security Alert</h1>
					<p>You are attempting to run the TikiWiki Installer. For your protection, this installer can be used only by a site administrator.</p>
					<p>To verify that you are a site administrator, enter your your <strong><em>database</em></strong> credentials (Username and Password) here. If you have forgotten your database credentials, they are located in the <code>&lt;INSTALL DIRECTORY&gt;\db\local.php</code> file.</p>
					<form method="post" action="tiki-install.php">
						<p><label for="dbuser">Database username</label>: <input type="text" name="dbuser"/></p>
						<p><label for="dbpass">Database password</label>: <input type="password" name="dbpass"/></p>
						<p><input type="submit" value=" Validate and Continue "/></p>
					</form>
				</td>
			</tr>
		</table>
	</div>
	<div id="tiki-bot" align="center">
		<a title="This is TikiWiki CMS/Groupware" href="http://info.tikiwiki.org" target="_blank"><img src="img/tiki/tikibutton2.png" alt="TikiWiki" border="0" /></a>
	</div>
</div>
</body>
</html>
<?php
}

?>
