<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-phpinfo.php,v 1.9 2007-03-06 19:29:50 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));

	$smarty->display("error.tpl");
	die;
}

// Display the template
//$smarty->assign('mid','tiki-phpinfo.tpl');
//$smarty->display("tiki.tpl");
phpinfo();

?>