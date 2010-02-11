<?php
include_once ('tiki-setup.php');

$access->check_feature('wiki_feature_3d');

$smarty->assign('page', $_REQUEST['page']);
$smarty->display('tiki-wiki3d.tpl');
