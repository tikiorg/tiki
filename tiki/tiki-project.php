<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-project.php,v 1.2 2005-01-22 22:54:55 mose Exp $

// Tiki Projects
// Damian Parker

require_once ('tiki-setup.php');
require_once ('lib/projects/projectlib.php');

if ($feature_projects != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_projects");

	$smarty->display("error.tpl");
	die;
}

if(!isset($_REQUEST["projectId"])) {
	$smarty->assign('msg', tra("No project reference assigned"));
	$smarty->display("error.tpl");
	die;
}

if ($userlib->object_has_one_permission($_REQUEST["projectId"], 'projects')) {
        $smarty->assign('individual', 'y');

        if ($tiki_p_admin != 'y') {
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'projects');
		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];
			if ($userlib->object_has_permission($user, $_REQUEST["projectId"], 'projects', $permName)) {
				$$permName = 'y';
				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';
				$smarty->assign("$permName", 'n');
			}
		}
	}
} elseif ($tiki_p_admin != 'y' && $feature_categories == 'y') {
	$perms_array = $categlib->get_object_categories_perms($user, 'projects', $_REQUEST['projectId']);
	if ($perms_array) {
		$is_categorized = TRUE;
		foreach ($perms_array as $perm => $value) {
			$$perm = $value;
		}
	} else {
		$is_categorized = FALSE;
	}
	if ($is_categorized && isset($tiki_p_view_categories) && $tiki_p_view_categories != 'y') {
		$smarty->assign('msg',tra("Permission denied you cannot view this page"));
		$smarty->display("error.tpl");
		die;
	}
}

if (isset($_REQUEST["admin"]) && $_REQUEST["admin"] == "yes" && $tiki_p_project_admin == "y") {
	$smarty->assign('adminview', 'y');
}

$project = $projectslib->get_project_by_id($_REQUEST["projectId"]);
$projectadmins = $projectslib->get_project_admins($_REQUEST["projectId"]);
$projectmembers = $projectslib->get_project_members($_REQUEST["projectId"]);

$smarty->assign('project', $project);
$smarty->assign('projectadmins', $projectadmins);
$smarty->assign('projectmembers', $projectmembers);

$cat_type = 'project';
$cat_objid = $_REQUEST["projectId"];
include_once ("categorize_list.php");
ask_ticket('tiki-project');

// Display the template
$smarty->assign('mid', 'tiki-project.tpl');
$smarty->display("tiki.tpl");

?>
