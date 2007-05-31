<?php
include_once ('tiki-setup.php');
$smarty->assign('page', $_REQUEST['page']);
$smarty->display('tiki-wiki3d.tpl');
?>
