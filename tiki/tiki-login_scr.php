<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-login_scr.php,v 1.4 2003-03-22 22:39:07 lrargerich Exp $

include_once("tiki-setup.php");

$smarty->assign('mid','tiki-login.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>