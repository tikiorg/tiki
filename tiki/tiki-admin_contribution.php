<?php
//$Header: /cvsroot/tikiwiki/tiki/tiki-admin_contribution.php,v 1.5 2006-09-14 14:28:45 sylvieg Exp $
require_once('tiki-setup.php');

if ($feature_contribution != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_contribution");
	$smarty->display("error.tpl");
	die;
}
include_once('lib/contribution/contributionlib.php');

if ($tiki_p_admin != 'y' && $tiki_p_admin_contribution != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}
if (isset($_REQUEST['setting'])) {
	check_ticket('admin_contribution');
	if (isset($_REQUEST['feature_contribution_mandatory']) && $_REQUEST['feature_contribution_mandatory'] == "on") {
		$tikilib->set_preference('feature_contribution_mandatory', 'y');
		$smarty->assign('feature_contribution_mandatory', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_mandatory', 'n');
		$smarty->assign('feature_contribution_mandatory', 'n');
	}
	if (isset($_REQUEST['feature_contribution_mandatory_forum']) && $_REQUEST['feature_contribution_mandatory_forum'] == "on") {
		$tikilib->set_preference('feature_contribution_mandatory_forum', 'y');
		$smarty->assign('feature_contribution_mandatory_forum', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_mandatory_forum', 'n');
		$smarty->assign('feature_contribution_mandatory_forum', 'n');
	}
	if (isset($_REQUEST['feature_contribution_mandatory_comment']) && $_REQUEST['feature_contribution_mandatory_comment'] == "on") {
		$tikilib->set_preference('feature_contribution_mandatory_comment', 'y');
		$smarty->assign('feature_contribution_mandatory_comment', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_mandatory_comment', 'n');
		$smarty->assign('feature_contribution_mandatory_comment', 'n');
	}
	if (isset($_REQUEST['feature_contribution_mandatory_blog']) && $_REQUEST['feature_contribution_mandatory_blog'] == "on") {
		$tikilib->set_preference('feature_contribution_mandatory_blog', 'y');
		$smarty->assign('feature_contribution_mandatory_blog', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_mandatory_blog', 'n');
		$smarty->assign('feature_contribution_mandatory_blog', 'n');
	}
	if (isset($_REQUEST['feature_contribution_display_in_comment']) && $_REQUEST['feature_contribution_display_in_comment'] == "on") {
		$tikilib->set_preference('feature_contribution_display_in_comment', 'y');
		$smarty->assign('feature_contribution_display_in_comment', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_display_in_comment', 'n');
		$smarty->assign('feature_contribution_display_in_comment', 'n');
	}
	
}
if (isset($_REQUEST['add']) && isset($_REQUEST['name'])) {
	check_ticket('admin_contribution');
	$contributionlib->add_contribution($_REQUEST['name'], isset($_REQUEST['description'])? $_REQUEST['description']: '');
}
if (isset($_REQUEST['replace']) && isset($_REQUEST['name']) && isset($_REQUEST['contributionId'])) {
	check_ticket('admin_contribution');
	$contributionlib->replace_contribution($_REQUEST['contributionId'], $_REQUEST['name'], isset($_REQUEST['description'])? $_REQUEST['description']: '');
	unset($_REQUEST['contributionId']);
}	
if (isset($_REQUEST['remove'])) {
	check_ticket('admin_contribution');
	$contributionlib->remove_contribution($_REQUEST['remove']);
}
if (isset($_REQUEST['contributionId'])) {
	$contribution = $contributionlib->get_contribution($_REQUEST['contributionId']);
	$smarty->assign('contribution', $contribution);
}
$contributions = $contributionlib->list_contributions();
$smarty->assign_by_ref('contributions', $contributions['data']);
ask_ticket('admin_contribution');
$smarty->assign('mid', 'tiki-admin_contribution.tpl');
$smarty->display("tiki.tpl");
 ?>