<?php

require_once("tiki-setup.php");

$smarty->assign('mid','tiki-ajax_example.tpl');
$smarty->display('tiki.tpl');

?>