<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/init.scripts/10-cookies_hack.php,v 1.1 2003-08-22 19:04:40 zaufi Exp $
 *
 * \brief Remove automatic quotes added to POST/COOKIE by PHP
 *
 */

// Copy-n-pasted form setup.php (it was __before__ Smarty init)

if (get_magic_quotes_gpc())
	foreach ($_REQUEST as $k => $v)
		if (!is_array($_REQUEST[$k]))
			$_REQUEST[$k] = stripslashes($v);

?>
