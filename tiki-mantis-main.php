<?php
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

include_once ('lib/mantislib.php');

if ($feature_mantis != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_mantis");

	$smarty->display("error.tpl");
	die;
}

if (!$user) {
	$smarty->assign('msg', tra("Must be logged to use this feature"));

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_mantis_view != 'y') {
	$smarty->assign('msg', tra("Permission denied"));

	$smarty->display("error.tpl");
	die;
} else {
	if (isset($_REQUEST["action"])) {

		switch ($_REQUEST["action"]) {
		case "setCurrentProject":
			$_SESSION["mantis_project"] = $_REQUEST["project_id"];
			break;
		}
	}

	if (isset($_SESSION["mantis_project"]))
		$p_project_id = $_SESSION["mantis_project"];

	$smarty->assign('currentProject', $p_project_id);
	$smarty->assign('projectOptions', $mantislib->project_option_list($user, $p_project_id));

	$smarty->assign('assignedOpenBugCount', $mantislib->get_assigned_open_bug_count($user, $p_project_id));
	$smarty->assign('reportedOpenBugCount', $mantislib->get_reported_open_bug_count($user, $p_project_id));

}


 
$smarty->assign('mid', 'tiki-mantis-main.tpl');
$smarty->display("tiki.tpl");

?>
