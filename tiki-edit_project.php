<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-edit_project.php,v 1.2 2005-01-22 22:54:54 mose Exp $

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

$project = $projectslib->get_project_by_id($_REQUEST["projectId"]);
$projectadmins = $projectslib->get_project_admins($_REQUEST["projectId"]);
$projectmembers = $projectslib->get_project_members($_REQUEST["projectId"]);

$smarty->assign('project', $project);
$smarty->assign('projectadmins', $projectadmins);
$smarty->assign('projectmembers', $projectmembers);

if ($_REQUEST["add"] == "filegal" && $tiki_p_project_admin == 'y') {

	if (isset($_REQUEST["create"])) {	
		check_ticket('tiki-edit-project');
		// create and set the perms
		include_once "lib/filegals/filegallib.php";
		
		$fgalId = $filegallib->replace_file_gallery(0, $feature_project_filegal_prefix.$project["projectName"],$_REQUEST["fgalDescription"], $user, 20, 'y', 'y', $_REQUEST['show_id'],$_REQUEST['show_icon'],$_REQUEST['show_name'],$_REQUEST['show_size'],$_REQUEST['show_description'],$_REQUEST['show_created'],$_REQUEST['show_dl'], 1024);
		// Now those perms
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'file galleries');
		foreach ($perms["data"] as $perm) {
			if (isset($_REQUEST["fgal:".$perm["permName"]]) && $_REQUEST["fgal:".$perm["permName"]] != 'na') {
				$userlib->assign_object_permission($_REQUEST["fgal:".$perm["permName"]], $fgalId, "file gallery", $perm["permName"]);
			}
		}
		//$userlib->assign_object_permission($group, $projectId, "project", $perm);
		// Now add the object to the project!
		$projectslib->add_object($_REQUEST["projectId"], "fgal", $fgalId);
		// And msg
		$smarty->assign("created", tra("File Gallery successfully added to the project."));
		
	} else {
		$smarty->assign("newobject", "filegal");
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'file galleries');
		$smarty->assign_by_ref('perms', $perms["data"]);
	}
}

$cat_type = 'project';
$cat_objid = $_REQUEST["projectId"];
include_once ("categorize_list.php");
ask_ticket('tiki-edit-project');

// Display the template
$smarty->assign('mid', 'tiki-edit_project.tpl');
$smarty->display("tiki.tpl");

?>
