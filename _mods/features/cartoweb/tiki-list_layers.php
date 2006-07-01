<?php
// Initialization
require_once ('tiki-setup.php');

include_once ('lib/map/layer.php');
include_once('lib/tree/layer_list_tree.php');
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
	if ($tiki_p_admin_layers != 'y') {
		$smarty->assign('msg', tra("Permission denied you cannot remove layers"));
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
if (isset($_REQUEST["layerId"])) {
        $layerId = $_REQUEST["layerId"];
	} else {
	$layerId='';	
}
if (isset($_REQUEST["mapId"])) {
        $mapId = $_REQUEST["mapId"];
	$smarty->assign('mapId',$mapId);
	} else {
	$mapId=0;	
}
// display get_layer_by_group 
$ctall=$layerlib->get_all_layers_ext($mapId);
$tree_nodes = array();
foreach ($ctall as $c) {
	if ($c["islayerGroup"] ==1) {
	$editlink="tiki-edit_layergroup.php";
	$layertitle=$c["name"];
	} else {
	$editlink="tiki-edit_layer.php";
	$layertitle='<img src="generated/icons/Sigfreed/World/'.$c["table"].'_class_0.png" />&nbsp;'.$c["name"];
	}
	$tree_nodes[] = array(
		"id" => $c["layerId"],
		"parent" => $c["layerGroupId"],
		"data" => $layertitle,
		"edit" =>'<a class="link" href="'.$editlink.'?layerId=' . $c["layerId"] . '" title="' . tra('edit'). '"><img border="0" src="img/icons/edit.gif" /></a>',
		"remove" =>'<a class="link" href="tiki-list_layers.php?layerId=' . $c["layerId"] . '&amp;removeCat=' . $c["layerId"] . '" title="' . tra('remove'). '"><img  border="0" src="img/icons2/delete.gif" /></a>',
		"perm" =>'<a class="link" href="tiki-objectpermissions.php?objectName=' . $c["name"].'&objectType=layers&permType=layers&objectId=' . $c["layerId"].' "title="' . tra('set permissions'). '"><img  border="0" src="img/icons/key.gif" /></a>',
		"layergroup" => $c["layergroup"],
		"layers" => $c["layers"]
	);
}

$debugger->var_dump('$tree_nodes');
$tm = new layerTreeMaker("layer");
$res = $tm->make_tree(0, $tree_nodes);
$smarty->assign('tree', $res);
 $layergroups=$layerlib->list_layerGroups($mapId,$layerId, $offset, $maxRecords, $sort_mode, $find);
 $smarty->assign('layergroups',$layergroups["data"]);
// end display
$listpages = $layerlib->list_layers($offset, $maxRecords, $sort_mode, $find, $mapId,$layerId);
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
ask_ticket('list-layers');

// Display the template
$smarty->assign('mid', 'tiki-list_layers.tpl');
$smarty->display("tiki.tpl");

?>
