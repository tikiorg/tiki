<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.8 2004-06-09 21:07:26 teedog Exp $

// Copyright (c) 2002-2004, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.8 2004-06-09 21:07:26 teedog Exp $
include_once ("tiki-setup.php");

if ($_REQUEST['user'] == 'admin') {
	$smarty->assign('showloginboxes', 'y');
}

$smarty->assign('mid', 'tiki-login.tpl');
$smarty->display("tiki.tpl");

?>