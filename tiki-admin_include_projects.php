<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_projects.php,v 1.2 2005-01-22 22:54:52 mose Exp $

// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_include_projects.php,v 1.2 2005-01-22 22:54:52 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["projects"])) {
	
	// Text boxes
	$pref_simple_values = array("feature_project_home_prefix", "feature_project_group_prefix", "feature_project_group_prefix_admin", "feature_project_filegal_prefix");

	foreach ($pref_simple_values as $svitem) {
		simple_set_value ($svitem);
	}

	//Drop downs
	$v = isset($_REQUEST['feature_project_admin_template']) ? $_REQUEST['feature_project_admin_template'] : '';
        $tikilib->set_preference('feature_project_admin_template', $v);
	$smarty->assign('feature_project_admin_template', $v);

	$v = isset($_REQUEST['feature_project_member_template']) ? $_REQUEST['feature_project_member_template'] : '';
	$tikilib->set_preference('feature_project_member_template', $v);
	$smarty->assign('feature_project_member_template', $v);
}

// Get group selectors
$smarty->assign("listgroups", $listgroups = $userlib->list_all_groups());

?>
