<?php

// Created by Víctor Codocedo Henriquez
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

if ($prefs['feature_workflow'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_workflow");

	$smarty->display("error.tpl");
	die;
}

if (!isset($_REQUEST['__user'])) {
    $smarty->assign('msg', tra("No user indicated"));
    $smarty->display("error.tpl");
    die;
}

if (!isset($_REQUEST['__title'])) {
    $smarty->assign('msg', tra("No title indicated"));
    $smarty->display("error.tpl");
    die;
}

if (!isset($_REQUEST['__comment'])) {
    $smarty->assign('msg', tra("No comment indicated"));
    $smarty->display("error.tpl");
    die;
}

if (!isset($_REQUEST['__timestamp'])) {
    $smarty->assign('msg', tra("No date indicated"));
    $smarty->display("error.tpl");
    die;
}

$smarty->assign('user',$_REQUEST['__user']);
$smarty->assign('title',$_REQUEST['__title']);
$smarty->assign('comment',$_REQUEST['__comment']);
$smarty->assign('timestamp',$_REQUEST['__timestamp']);
$smarty->assign('jPrint',"print();");
$smarty->assign('jClose',"window.close();");
$smarty->display("tiki-g-view_comment.tpl");

?>
