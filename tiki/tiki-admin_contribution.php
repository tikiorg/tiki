<?php
//$Header: /cvsroot/tikiwiki/tiki/tiki-admin_contribution.php,v 1.1 2006-03-02 15:15:51 sylvieg Exp $
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
	simple_set_toggle ('feature_contribution_mandatory');
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