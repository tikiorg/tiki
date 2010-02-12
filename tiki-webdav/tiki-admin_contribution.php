<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_feature('feature_contribution');

include_once ('lib/contribution/contributionlib.php');
$access->check_permission(array('tiki_p_admin_contribution'));

if (isset($_REQUEST['setting'])) {
	check_ticket('admin_contribution');
	if (isset($_REQUEST['feature_contribution_mandatory']) && $_REQUEST['feature_contribution_mandatory'] == "on") {
		$tikilib->set_preference('feature_contribution_mandatory', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_mandatory', 'n');
	}
	if (isset($_REQUEST['feature_contribution_mandatory_forum']) && $_REQUEST['feature_contribution_mandatory_forum'] == "on") {
		$tikilib->set_preference('feature_contribution_mandatory_forum', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_mandatory_forum', 'n');
	}
	if (isset($_REQUEST['feature_contribution_mandatory_comment']) && $_REQUEST['feature_contribution_mandatory_comment'] == "on") {
		$tikilib->set_preference('feature_contribution_mandatory_comment', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_mandatory_comment', 'n');
	}
	if (isset($_REQUEST['feature_contribution_mandatory_blog']) && $_REQUEST['feature_contribution_mandatory_blog'] == "on") {
		$tikilib->set_preference('feature_contribution_mandatory_blog', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_mandatory_blog', 'n');
	}
	if (isset($_REQUEST['feature_contribution_display_in_comment']) && $_REQUEST['feature_contribution_display_in_comment'] == "on") {
		$tikilib->set_preference('feature_contribution_display_in_comment', 'y');
	} else {
		$tikilib->set_preference('feature_contribution_display_in_comment', 'n');
	}
	if (isset($_REQUEST['feature_contributor_wiki']) && $_REQUEST['feature_contributor_wiki'] == "on") {
		$tikilib->set_preference('feature_contributor_wiki', 'y');
	} else {
		$tikilib->set_preference('feature_contributor_wiki', 'n');
	}
}
if (isset($_REQUEST['add']) && isset($_REQUEST['name'])) {
	check_ticket('admin_contribution');
	$contributionlib->add_contribution($_REQUEST['name'], isset($_REQUEST['description']) ? $_REQUEST['description'] : '');
}
if (isset($_REQUEST['replace']) && isset($_REQUEST['name']) && isset($_REQUEST['contributionId'])) {
	check_ticket('admin_contribution');
	$contributionlib->replace_contribution($_REQUEST['contributionId'], $_REQUEST['name'], isset($_REQUEST['description']) ? $_REQUEST['description'] : '');
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
