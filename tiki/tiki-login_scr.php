<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.5 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

# $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.5 2003-08-07 04:33:57 rossta Exp $
include_once ("tiki-setup.php");

$smarty->assign('mid', 'tiki-login.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>