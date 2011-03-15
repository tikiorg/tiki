<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$in_installer = 1;
if (!isset($title)) $title = 'Tiki Installer';
if (!isset($content)) $content = 'No content specified. Something went wrong.<br/>Please tell your administrator.<br/>If you are the administrator, you may want to check for / file a bug report.';
if (!isset($dberror)) $dberror = false;

// Check that PHP version is at least 5
if (version_compare(PHP_VERSION, '5.1.0', '<')) {
	$title = 'PHP 5.1 is required';
	$content = '<p>Please contact your system administrator ( if you are not the one ;) ).</p>';
	createPage($title, $content);
}

include_once('db/tiki-db.php');	// to set up multitiki etc if there

// if tiki installer is locked (probably after previous installation) display notice
if (file_exists('db/'.$tikidomainslash.'lock')) {
	$title = 'Tiki Installer Disabled';
	$td = empty($tikidomain)? '': '/'.$tikidomain;
	$content = '
							<p>As a security precaution, the Tiki Installer has been disabled. To re-enable the installer:</p>
							<div style="border: solid 1px #ccc; margin: 1em auto; width: 40%;">
								<ol style="text-align: left">
									<li>Use your file manager application to find the directory where you have unpacked your Tiki and remove the <strong><code>lock</code></strong> file which was created in the <strong><code>db'.$td.'</code></strong> folder.</li>
									<li>Re-run <strong><a href="tiki-install.php'.(empty($tikidomain)?'':"?multi=$tikidomain").'" title="Tiki Installer">tiki-install.php'.(empty($tikidomain)?'':"?multi=$tikidomain").'</a></strong>.</li>
								</ol>
							</div>';
	createPage($title, $content);
}

$tikiroot = str_replace('\\','/',dirname($_SERVER['PHP_SELF']));
$session_params = session_get_cookie_params();
session_set_cookie_params($session_params['lifetime'], $tikiroot);
unset($session_params);
session_start();

require_once 'lib/core/TikiDb/Adodb.php';
require_once 'lib/core/TikiDb/Pdo.php';

// Were database details defined before? If so, load them
if (file_exists('db/'.$tikidomainslash.'local.php')) {
	include 'db/'.$tikidomainslash.'local.php';

	// In case of replication, ignore it during installer.
	unset( $shadow_dbs, $shadow_user, $shadow_pass, $shadow_host );

	// check for provided login details and check against the old, saved details that they're correct
	if (isset($_POST['dbuser'], $_POST['dbpass'])) {
		if (($_POST['dbuser'] == $user_tiki) && ($_POST['dbpass'] == $pass_tiki)) {
			$_SESSION['accessible'] = true;
		}
	}
} else {
	// No database info found, so it's a first-install and thus installer is accessible
	$_SESSION['accessible'] = true;
}

if (isset($_SESSION['accessible'])) {
	// allowed to access installer, include it
	$logged = true;
	$admin_acc = 'y';
	include_once 'installer/tiki-installer.php';
} else {
	// Installer knows db details but no login details were received for this script.
	// Thus, display a form.
	$title = 'Tiki Installer Security Precaution';
	$content = '
							<p>&nbsp;</p>
							<p>You are attempting to run the Tiki Installer. For your protection, this installer can be used only by a site administrator.</p>
							<p>To verify that you are a site administrator, enter your <strong><em>database</em></strong> credentials (database username and password) here.</p>
							<p>If you have forgotten your database credentials, find the directory where you have unpacked your Tiki and have a look inside the <strong><code>db</code></strong> folder into the <strong><code>local.php</code></strong> file.</p>
							<form method="post" action="' . $_SERVER['REQUEST_URI'] . '">
								<p><label for="dbuser">Database username</label>: <input type="text" id="dbuser" name="dbuser" /></p>
								<p><label for="dbpass">Database password</label>: <input type="password" id="dbpass" name="dbpass" /></p>
								<p><input type="submit" value=" Validate and Continue " /></p>
							</form>
							<p>&nbsp;</p>';
	createPage($title, $content);
}



function createPage($title, $content){
	echo <<<END
<!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link type="text/css" rel="stylesheet" href="styles/fivealive.css" />
		<title>$title</title>
	</head>
	<body class="tiki_wiki fixed_width">
	<div id="fixedwidth">
	<div id="outer_wrapper">
		<div id="header_outer">
			<div id="header_container">
				<header id="header">
					<div id="header_fixedwidth" class="clearfix fixedwidth">
						<div class="content clearfix modules" id="top_modules">
							<div id="sitelogo">
								<img alt="Site Logo" src="img/tiki/Tiki_WCG.png"  />
							</div>
							<div id="tiki-top" class="clearfix" style="position: absolute; top: 166px;">
								<h1 style="color: #fff; text-shadow: 3px 2px 0 #781437; margin: 0; padding: 0; line-height: 1.6;">$title</h1>
							</div>
						</div>
					</div>
				</header>
			</div>
		</div>	
			<div class="middle_outer">
			<div id="middle" >
				<div id="tiki-top"></div>
					<div id="tiki-center" style="display: table; width: 990px; text-align: center;">
						$content
					</div>
				</div>
				</div>
			</div><!--
		<footer id="footer" style="margin-top: 50px;">
	<div class="footer_liner">
		<div class="footerbgtrap fixedwidth" style="padding: 10px 0;">
			<a href="http://tiki.org" target="_blank" title="Powered by Tiki Wiki CMS Groupware"><img src="img/tiki/tikibutton.png" alt="Powered by Tiki Wiki CMS Groupware" /></a>
		</div>
	</div>
</footer>-->
</div>
	</body>
</html>
END;
	die;
}
