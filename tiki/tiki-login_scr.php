<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.6 2003-11-17 15:44:29 mose Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.6 2003-11-17 15:44:29 mose Exp $
include_once ("tiki-setup.php");

$smarty->assign('mid', 'tiki-login.tpl');
$smarty->display("tiki.tpl");

?>