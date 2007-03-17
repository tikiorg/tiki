<?php
// Initialization
require_once ('tiki-setup.php');

include_once ('lib/map/editorlib.php');

// CHECK FEATURE MAP HERE
if ($feature_maps != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_maps");

	$smarty->display("error.tpl");
	die;
}

// IF NOT LOGGED aND NOT ADMIN BAIL OUT
if (!$user) {
	$smarty->assign('msg', tra("You are not logged in"));

	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST["remove"])) {
	if ($tiki_p_admin_maps != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot remove maps"));
		$smarty->display("error.tpl");
		die;
	}
  $area = 'delbanner';
  if ($feature_ticketlib2 != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
    key_check($area);
		$layerlib->remove_banner($_REQUEST["remove"]);
  } else {
    key_get($area);
  }
}

// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}

$smarty->assign_by_ref('sort_mode', $sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}

$smarty->assign('find', $find);

// Get a list of last changes to the Wiki database
$who = 'admin';
/*
if($tiki_p_admin_maps == 'y') {
  $who = 'admin';
} else {
  $who = $user;
}
*/
$listpages = $layerlib->list_maps($offset, $maxRecords, $sort_mode, $find, $who);
// If there're more records then assign next_offset
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($listpages["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$smarty->assign_by_ref('listpages', $listpages["data"]);
//print_r($listpages["data"]);
ask_ticket('list-maps');

// Display the template
$smarty->assign('mid', 'tiki-mapeditor_maps.tpl');
$smarty->display("tiki.tpl");

?>
