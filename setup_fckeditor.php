<?php
include 'tiki-setup.php';
header('Content-type: application/javascript');

$fckstyle = "styles/$style";
$smarty->assign('fckstyle',$fckstyle);

$smarty->display('setup_fckeditor.tpl');
?>
