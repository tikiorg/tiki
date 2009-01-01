<?php
include_once ('tiki-setup.php');
if($prefs['wiki_feature_3d'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': wiki_feature_3d');
	$smarty->display('error.tpl');
	die;  
}
$smarty->assign('page', $_REQUEST['page']);
$smarty->display('tiki-wiki3d.tpl');
?>
