<?php

// $Id$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

function installer_is_accessible()
{
	if( ! isset( $_SESSION['accessible'] ) )
		return false;

	return true;
}

if (!isset($title)) $title = '';
if (!isset($content)) $content = '';
if (!isset($dberror)) $dberror = false;

if (version_compare(PHP_VERSION, '5.0.0', '<')) {
	$title='PHP5 is required for Tiki 3.0';
	$content='<p>Please contact your system administrator ( if you are not one ;) ).</p>';
	createPage($title,$content);
}

if ($dberror===true) {
	createPage($title,$content);
}

if( file_exists( 'db/lock' ) ) {
	$title='Tiki Installer Disabled';
	$content='
							<p>As a security precaution, the Tiki Installer has been disabled. To re-enable the installer:</p>
							<div style="border: solid 1px #ccc; margin: 1em auto; width: 40%;">
								<ol style="text-align: left">
									<li>Use your file manager application to find the directory where you have unpacked your Tiki and remove the <strong><code>lock</code></strong> file which was created in the <strong><code>db</code></strong> folder.</li>
									<li>Re-run <strong><a href="tiki-install.php" title="Tiki Installer">tiki-install.php</a></strong>.</li>
								</ol>
							</div>';
	createPage($title,$content);
}

session_start();

if ( file_exists( 'db/local.php' ) ) {

	include('db/local.php');
	include_once('lib/adodb/adodb.inc.php');
	$dbTiki = ADONewConnection($db_tiki);

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
	$title='Tiki Installer Security Precaution';
	$content='
							<p>&nbsp;</p>
							<p>You are attempting to run the Tiki Installer. For your protection, this installer can be used only by a site administrator.</p>
							<p>To verify that you are a site administrator, enter your <strong><em>database</em></strong> credentials (database username and password) here.</p>
							<p>If you have forgotten your database credentials, find the directory where you have unpacked your Tiki and have a look inside the <strong><code>db</code></strong> folder into the <strong><code>local.php</code></strong> file.</p>
							<form method="post" action="tiki-install.php">
								<p><label for="dbuser">Database username</label>: <input type="text" name="dbuser"/></p>
								<p><label for="dbpass">Database password</label>: <input type="password" name="dbpass"/></p>
								<p><input type="submit" value=" Validate and Continue "/></p>
							</form>
							<p>&nbsp;</p>';
	createPage($title,$content);
}

function createPage($title,$content){
	echo <<<END
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" href="styles/strasa.css" rel="stylesheet" />
		<style type="text/css" media="screen">
html {
	background-color: #fff;
}
#centercolumn {
	padding: 4em 10em;
}
		</style>
		<title>$title</title>
	</head>
	<body class="tiki_wiki" style="text-align: center;">
		<div id="siteheader">
			<div id="sitelogo" style="text-align: center; padding-left: 70px;">
				<img style="border: medium none ;" alt="Site Logo" src="img/tiki/tiki3.png" />
			</div>
		</div>
		<div id="tiki-main">
			<div id="tiki-mid">
				<table id="tiki-midtbl" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tr>
						<td id="centercolumn" style="text-align:center; vertical-align:top">
							<h1>$title</h1>
							$content
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
END;
	die;
}
