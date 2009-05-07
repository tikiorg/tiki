<?php
// $Id$
include_once ('tiki-setup.php');
if ($prefs['feature_group_watches'] != 'y') {
	$smarty->assign('msg', tra('This feature is disabled').': feature_group_watches');
	$smarty->display('error.tpl');
	die;
}
if ($tiki_p_admin != 'y' && $tiki_p_admin_users != 'y' ) {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display("error.tpl");
	die;
}
if (!isset($_REQUEST['objectId']) || empty($_REQUEST['objectType']) || !isset($_REQUEST['objectName']) || !isset($_REQUEST['watch_event']) || !isset($_REQUEST['objectHref'])) {
	$smarty->assign('msg', tra('Not enough information to display this page'));
	$smarty->display('error.tpl');
	die;
}
$auto_query_args = array('objectId', 'objectType', 'objectName', 'watch_event', 'referer', 'objectHref');
$all_groups = $userlib->list_all_groups();
$smarty->assign_by_ref('all_groups', $all_groups);

if (!isset($_REQUEST['referer']) && isset($_SERVER['HTTP_REFERER'])) {
	$_REQUEST['referer'] = $_SERVER['HTTP_REFERER'];
}
if (isset($_REQUEST['referer'])) {
	$smarty->assign('referer', $_REQUEST['referer']);
}

if (isset($_REQUEST['assign'])) {
	$addedGroups = array();
	$deletedGroups = array();
	if (!isset($_REQUEST['checked'])) $_REQUEST['checked'] = array();
	$old_watches = $tikilib->get_groups_watching($_REQUEST['objectType'], $_REQUEST['objectId'], $_REQUEST['watch_event']);
	check_ticket('object_watches');
	foreach ($all_groups as $g) {
		if (in_array($g, $_REQUEST['checked']) && !in_array($g, $old_watches)) { 
			$tikilib->add_group_watch($g, $_REQUEST['watch_event'], $_REQUEST['objectId'], $_REQUEST['objectType'], $_REQUEST['objectName'], $_REQUEST['objectHref']);
			$addedGroups[] = $g;
		} elseif (!in_array($g, $_REQUEST['checked']) && in_array($g, $old_watches)) {
			$tikilib->remove_group_watch($g, $_REQUEST['watch_event'], $_REQUEST['objectId'], $_REQUEST['objectType'], $_REQUEST['objectName'], $_REQUEST['objectHref']);
			$deletedGroups[] = $g;
		}
	}
	$smarty->assign_by_ref('addedGroups', $addedGroups);
	$smarty->assign_by_ref('deletedGroups', $deletedGroups);
	$group_watches = $_REQUEST['checked'];
} else {
	$group_watches = $tikilib->get_groups_watching($_REQUEST['objectType'], $_REQUEST['objectId'], $_REQUEST['watch_event']);
}
$smarty->assign_by_ref('group_watches', $group_watches);

ask_ticket('object_watches');
$smarty->assign('mid','tiki-object_watches.tpl');
$smarty->display('tiki.tpl');