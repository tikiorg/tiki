<?php

// $HEADER$

include_once ('tiki-setup.php');

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$base_url = preg_replace('/\/tiki-wiki3d.php.+$/','',$base_url);

$smarty->assign('base_url',$base_url);

$smarty->assign('page',$_REQUEST['page']);
$smarty->display('tiki-wiki3d.tpl');

?>
