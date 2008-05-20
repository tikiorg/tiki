<?php

// $Id:

echo '<html><body><pre><p>';
if (isset($_REQUEST['error']) and !is_null($_REQUEST['error'])) {
	echo htmlentities(strip_tags($_REQUEST["error"]), ENT_QUOTES, "UTF-8");	
} else {
	echo 'There was an unspecified error.  Please go back and try again.';
}

echo '</p>
<form name="loginbox" action="tiki-login.php?page=tikiIndex" method="post">
user: <input type="text" name="user"  size="20" /><br />
pass: <input type="password" name="pass" size="20" /><br />
<input type="submit" name="login" value="login" /></form>';

echo '<p><a href="javascript:history.back()">Go back</a></p></pre></body></html>';

?>
