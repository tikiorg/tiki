<?php

include_once ('tiki-setup.php');

$base_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$base_url = preg_replace('/\/tiki-wiki3d.php.+$/','',$base_url);

// This is just during early development for it to work
// in my domain to remote users, should be removed later
$smarty->assign('base_url',preg_replace('/kriconet.prv/','kriconet.com.br',$base_url));

$smarty->assign('page',$_REQUEST['page']);
$smarty->display('tiki-wiki3d.tpl');

?>
