<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.2 2003-01-04 19:34:16 rossta Exp $

include_once("tiki-setup.php");

$smarty->assign('mid','tiki-login.tpl');
$smarty->display('tiki.tpl');
?>