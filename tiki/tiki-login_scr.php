<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.3 2003-02-16 12:58:37 rossta Exp $

include_once("tiki-setup.php");

$smarty->assign('mid','tiki-login.tpl');
$smarty->display('tiki.tpl');
?>