<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
require_once ('tiki-setup.php');
require_once ('lib/workspaces/typeslib.php');
require_once ('lib/workspaces/workspacelib.php');

if ($tiki_p_admin != 'y' && (!isset ($tiki_p_admin_workspace) || $tiki_p_admin_workspace != 'y')) {
	$smarty->assign('msg', tra("Permission denied"));
	$smarty->display("error.tpl");
	die;
}

$workspacesLib = new WorkspaceLib($dbTiki);
$wstypesLib = new WorkspaceTypesLib($dbTiki);

$typesAll = $wstypesLib->list_active_types();

if (isset ($_REQUEST["send"])) {
	$workspace = array();
	$workspace["closed"] = "n";
	if (isset ($_REQUEST["closed"])) {
		$workspace["closed"] = "y";
	}

	$workspace["isuserws"] = "n";
	if (isset ($_REQUEST["isuserws"])) {
		$workspace["isuserws"] = $_REQUEST["isuserws"];
	}

	$workspace["hide"] = "n";
	if (isset ($_REQUEST["hide"])) {
		$workspace["hide"] = "y";
	}
	
	if (isset ($_REQUEST["parentId"]) && $_REQUEST["parentId"]!="") {
		$workspace["parentId"] = $_REQUEST["parentId"];
	}elseif (isset ($_REQUEST["viewWS"]) && $_REQUEST["viewWS"]!="") {
		$workspace["parentId"] = $_REQUEST["viewWS"];
	} else {
		$workspace["parentId"] = 0;
	}
	if (isset ($_REQUEST["categoryId"])) {
		$workspace["categoryId"] = $_REQUEST["categoryId"];
	} else {
		$workspace["categoryId"] = null;
	}
	
	if (isset ($_REQUEST["workspaceId"]) && ($_REQUEST["workspaceId"] != "")) {
		$workspace["workspaceId"] = $_REQUEST["workspaceId"];
	}else{
		$workspace["workspaceId"] = null;
	}
	
	$exit = false;
	if (isset ($_REQUEST["code"]) && ($_REQUEST["code"] != "")) {
		$ws = $workspacesLib->get_workspace_by_code($_REQUEST["code"]);

		if ((!isset($_REQUEST["workspaceId"]) || $_REQUEST["workspaceId"]=="")  && isset($ws) && $ws!=""){ //Other workspace with the same code
			$smarty->assign('page_error_msg', tra("Code in use, please select a different code"));
			$exit = true;	
		}
		$workspace["code"] = $_REQUEST["code"];
	}else{
		$smarty->assign('page_error_msg', tra("Code not selected"));
		$exit = true;
	}
	
	if (isset($_REQUEST["name"]) && $_REQUEST["name"]!=""){
		$workspace["name"] = $_REQUEST["name"];
	}elseif(!$exit){
		$workspace["name"] = $_REQUEST["code"];
	}
	
	if (isset($_REQUEST["desc"])){
		$workspace["description"] = $_REQUEST["desc"];
	}else{
		$workspace["description"] = "";
	}
	//echo "FECHA:".$_REQUEST["startDate"];
	$format_error = FALSE;
	if (isset($_REQUEST["startDate"]) && $_REQUEST["startDate"]!=""){
		$workspace["startDate"] = $_REQUEST["startDate"];
	}else{
		if (isset($_REQUEST["start_freeform"])and $_REQUEST["start_freeform"]) {
			if (($workspace["startDate"] = strtotime($_REQUEST["start_freeform"])) == -1) {
				$format_error = TRUE;
			}
		}
		if (!isset($workspace["startDate"]) || $format_error) {
			$workspace["startDate"] = mktime($_REQUEST["starth_Hour"], $_REQUEST["starth_Minute"],
					0, $_REQUEST["start_Month"], $_REQUEST["start_Day"], $_REQUEST["start_Year"]);
		}
	}
	
	if (isset($_REQUEST["endDate"]) && $_REQUEST["endDate"]!=""){
		$workspace["endDate"] = $_REQUEST["endDate"];
	}else{
				if (isset($_REQUEST["end_freeform"])and $_REQUEST["end_freeform"]) {
			if (($workspace["endDate"] = strtotime($_REQUEST["end_freeform"])) == -1) {
				$format_error = TRUE;
			}
		}
		if (!isset($workspace["endDate"]) || $format_error) {
			$workspace["endDate"] = mktime($_REQUEST["endh_Hour"], $_REQUEST["endh_Minute"],
					0, $_REQUEST["end_Month"], $_REQUEST["end_Day"], $_REQUEST["end_Year"]);
			
		}
	}
	
	if (isset($_REQUEST["type"]) && $_REQUEST["type"]!=""){
		$workspace["type"] = $_REQUEST["type"];
	}else{
		$workspace["type"] = null; 
	}

	if (isset($_REQUEST["owner"]) && $_REQUEST["owner"]!=""){
		$workspace["owner"] = $_REQUEST["owner"];
	}else{
		$workspace["owner"] = null; 
	}
	$workspace["created"] = date("U");
	 
	if (!$exit && isset($_REQUEST["workspaceId"]) && ($_REQUEST["workspaceId"] != "")) {
		$workspacesLib->update_workspace_info($workspace["workspaceId"], $workspace["code"], $workspace["name"], $workspace["description"], $workspace["startDate"], $workspace["endDate"], $workspace["closed"], $workspace["parentId"], $workspace["type"],null, null, $workspace["owner"], $workspace["isuserws"], $workspace["hide"]);
	} elseif(!$exit) {
		$workspacesLib->create_workspace($workspace["code"], $workspace["name"], $workspace["description"], $workspace["startDate"], $workspace["endDate"], $workspace["closed"], $workspace["parentId"], $workspace["type"], null, $workspace["owner"], $workspace["isuserws"], $workspace["hide"]);
	}else{
		$smarty->assign_by_ref('workspace', $workspace);
	}
} else
	if (isset ($_REQUEST["edit"])) {
		$workspace = $workspacesLib->get_workspace_by_id($_REQUEST["edit"]);
		$path = $workspacesLib->get_workspace_path($workspace["parentId"]);
		$workspace["path"] = $path;
		$smarty->assign_by_ref('workspace', $workspace);
	} else
		if (isset ($_REQUEST["delete"])) {
			$area = 'delworkspace';
			if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
				key_check($area);
				$workspacesLib->remove_workspace($_REQUEST["delete"]);
			} else {
				key_get($area);
			}
		}
$viewWS = 0;
if (isset ($_REQUEST["viewWS"])) {
	$viewWS = $_REQUEST["viewWS"];
}

$path = $workspacesLib->get_workspace_path($viewWS);
$viewCategoryId = $path[count($path)-1]["categoryId"];

if (!isset ($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = 'name_desc';
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

if (!isset ($_REQUEST["numrows"])) {
	$numrows = $maxRecords;
} else {
	$numrows = $_REQUEST["numrows"];
}

$smarty->assign_by_ref('numrows', $numrows);

if (!isset ($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset ($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

if ($find==''){
	$workspacesData = $workspacesLib->get_child_workspaces($offset, $numrows, $sort_mode,$viewWS);
}else{
	$workspacesData = $workspacesLib->get_workspace_list($offset, $numrows, $sort_mode, $find);
}

$cant_pages = ceil($workspacesData["cant"] / $numrows);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $numrows));

if ($workspacesData["cant"] > ($offset + $numrows)) {
	$smarty->assign('next_offset', $offset + $numrows);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $numrows);
} else {
	$smarty->assign('prev_offset', -1);
}

if (($firstDayofWeek = $tikilib->get_user_preference($user, "")) == "") { /* 0 for Sundays, 1 for Mondays */
	$strRef = "First day of week: Sunday (its ID is 0) - translators you need to localize this string!";
	//get_strings tra("First day of week: Sunday (its ID is 0) - translators you need to localize this string!");
	if (($str = tra($strRef)) != $strRef) {
		$firstDayofWeek = ereg_replace("[^0-9]", "", $str);
		if ($firstDayofWeek < 0 || $firstDayofWeek > 9)
			$firstDayofWeek = 0;
	} else
		$firstDayofWeek = 0;
}
$smarty->assign('firstDayofWeek', $firstDayofWeek);

$strRef = tra("%H:%M %Z");
if (strstr($strRef, "%h") || strstr($strRef, "%g"))
	$timeFormat12_24 = "12";
else
	$timeFormat12_24 = "24";
$smarty->assign('timeFormat12_24', $timeFormat12_24);

if (isset ($workspace) && $workspace) {
       	$smarty->assign('startDate', $workspace["startDate"]);
	$smarty->assign('endDate', $workspace["endDate"]);
	$smarty->assign_by_ref('created', $workspace["created"]);
} else {
 	$date = $tikilib->now;
	$smarty->assign('startDate', $date);
	$smarty->assign('endDate', $date);
}
$smarty->assign('daformat', $tikilib->get_long_date_format()." ".tra("at")." %H:%M");
$smarty->assign_by_ref('typesAll', $typesAll);

$workspaces = array();
if (isset($workspacesData) && isset($workspacesData["data"])){
	foreach ($workspacesData["data"] as $key => $ws) {
		$ws["href"] = $workspacesLib->get_href_to_workspace($ws);
		$workspaces[$ws["workspaceId"]] = $ws;
	}
}
$smarty->assign_by_ref('workspaces', $workspaces);
//$smarty->assign_by_ref('workspace',$workspace);
$smarty->assign_by_ref('path', $path);
$smarty->assign_by_ref('viewWS', $viewWS);
$smarty->assign_by_ref('viewCategoryId',$viewCategoryId);

include_once ('tiki-jscalendar.php');

$smarty->assign('mid', 'tiki-workspaces_admin.tpl');
$smarty->display('tiki.tpl');
?>