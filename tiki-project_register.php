<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-project_register.php,v 1.2 2005-01-22 22:54:55 mose Exp $

// Tiki Projects
// Damian Parker

require_once ('tiki-setup.php');
require_once ('lib/projects/projectlib.php');

if ($feature_projects != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_projects");

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["save"])) {
	check_ticket('tiki-project');
	if (!isset($_REQUEST["projectName"])) {
		$smarty->assign('msg', tra("You must provide a project name"));
		$smarty->display("error.tpl");
		die;
	}

	if (!isset($_REQUEST["projectDescription"])) {
		$smarty->assign('msg', tra("You must provide a project description"));
		$smarty->display("error.tpl");
		die;
	}

	if ($projectslib->project_exists($_REQUEST["projectName"])) {
		$smarty->assign('msg', tra("A project with that name already exists"));
		$smarty->display("error.tpl");
		die;
	}

	// Now save the project
	$projectslib->add_new_project($_REQUEST["projectName"], $_REQUEST["projectDescription"]);

	$smarty->assign('save', 'true');

}

$cat_type = 'project';
$cat_objid = 0;
include_once ("categorize_list.php");

ask_ticket('tiki-project');

// Display the template
$smarty->assign('mid', 'tiki-project_register.tpl');
$smarty->display("tiki.tpl");

?>
