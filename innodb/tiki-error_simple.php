<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

echo '<html><head><title>maintenance</title><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body><pre><p>';
if (isset($_REQUEST['error']) and !is_null($_REQUEST['error'])) {

	$_REQUEST["error"] = substr($_REQUEST["error"],0,256);
	echo htmlentities(strip_tags($_REQUEST["error"]), ENT_QUOTES, 'UTF-8' );	

} else {
	echo 'There was an unspecified error.  Please go back and try again.';
}

echo '</p>
<form name="loginbox" action="tiki-login.php?page=tikiIndex" method="post">
User: <input type="text" name="user"  size="20" /><br />
Pass: <input type="password" name="pass" size="20" /><br />
<input type="submit" name="login" value="login" /></form>';

echo '<p><a href="javascript:history.back()">Go back</a></p></pre></body></html>';
