<?php
chdir('../../../..');
require_once('tiki-setup.php');

$tikipath = dirname(__FILE__);
$tikiroot = dirname($_SERVER['PHP_SELF']);
$smarty->assign('tikipath',$tikipath);
$smarty->assign('tikiroot',$tikiroot);
$smarty->template_dir = $tikipath.'/templates/';

$listpages = $tikilib->list_pages(0, -1, 'pageName_asc', '', '', true, false);
$smarty->assign('listpages',$listpages['data']);

$smarty->display('fck_tikilink.tpl');
?>
