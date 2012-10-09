<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

include_once('lib/init/tra.php');

if (!empty($_REQUEST['error'])) {
	$error = substr($_REQUEST["error"], 0, 256);
} else {
	$error = tra('There was an unspecified error.  Please go back and try again.');
}
if (!empty($_REQUEST['title'])) {
	$title = $_REQUEST['title'];
} else {
	$title = tra('Maintenance');
}

$login = '<form name="loginbox" action="tiki-login_scr.php" method="post"><table><tr><td>' .
	tra('User:') . '</td><td><input type="text" name="user"  size="20" /></td></tr><tr><td>' .
	tra('Pass:') . '</td><td><input type="password" name="pass" size="20" /></td></tr><tr><td align="center"><input type="submit" name="login" value="login" /></td></tr></table></form>';

$back = '<p><a href="javascript:history.back()">' . tra('Go back') . '</a></p>';

if (file_exists('templates/styles/site_closed_local.html')) {
	$html = file_get_contents('templates/styles/site_closed_local.html');
} else {
	$html = file_get_contents('templates/styles/site_closed.html');
}

$html = str_replace('{error}', $error, $html);
$html = str_replace('{title}', $title, $html);
$html = str_replace('{login}', $login, $html);
$html = str_replace('{back}', $back, $html);

header("HTTP/1.0 302 Moved Temporarily");

echo $html;
