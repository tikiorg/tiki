<?php
// Initialization
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()
include_once ('lib/map/editorlib.php');

if (!isset($layerlib)) {
	$layerlib = new LayerLib($dbTiki,$dbTiki2);
}

// CHECK FEATURE MAPS AND ADMIN PERMISSION HERE
if ($feature_maps != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_layers");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_map_edit != 'y') {
	$smarty->assign('msg', tra("You do not have permissions to edit layers"));

	$smarty->display("error.tpl");
	die;
}
	if (isset($_REQUEST["cat_restrict_child"])) {
		$cat_restrict_child = $_REQUEST["cat_restrict_child"];
	} else {
		$cat_restrict_child = 0;
	}
	if (isset($_REQUEST["layerId"])) {
		$cat_objid = $_REQUEST["layerId"];
		$layerId=$_REQUEST["layerId"];
		} else {
		$cat_objid ="";
	}
		
	if (isset($_REQUEST["description"])) {
		$cat_desc = $_REQUEST["description"];
	} else {
		$cat_desc ="";
	}
	if (isset($_REQUEST["cat_type"])) {
		$cat_type = $_REQUEST["cat_type"];
	} else {
		$cat_type ="layer";
	}
	$smarty->assign('individual', 'n');
	include_once ('lib/categories/categlib.php');
	//if(isset($_REQUEST["layerId"])) {
	//	$categories=$categlib->get_object_categories('layer', $layerId);
	//	if($categories) {
	//		$gateways=$categlib->list_category_objects($categories[0],0,$maxRecords,'','wiki page');
	//		$smarty->assign('gateways', $gateways['data']);
	//	}
	//}
if (isset($_REQUEST["layerId"]) && $_REQUEST["layerId"] > 0) {
	$info = $layerlib->get_layergroup($_REQUEST["layerId"]);
	$mapinfo = $layerlib->get_map($info["mapId"]);
	//$map_categories=$categlib->get_object_categories('map', $info["mapId"]);
	$cat_restrict_child=$map_categories[0];
	$smarty->assign('cat_restrict_child',$cat_restrict_child);
	$layerId=$_REQUEST["layerId"];
	if (!$info) {
		$smarty->assign('msg', tra("Map not found"));

		$smarty->display("error.tpl");
		die;
	}
	$smarty->assign('individual', 'n');
	if ($userlib->object_has_one_permission($layerId, 'layer')) {
		$smarty->assign('individual', 'y');

	if ($tiki_p_admin != 'y') {
		// Now get all the permissions that are set for this type of permissions 'image gallery'
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'layer');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $layerId, 'layer', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	} else {
		$gateway='';
	}
}
	// Check user is admin or the name
	if (($user != $info["author"]) && ($tiki_p_map_edit != 'y')) {
		$smarty->assign('msg', tra("You do not have permission to edit this layer"));

		$smarty->display("error.tpl");
		die;
	}

	$smarty->assign('layerId', $info["layerId"]);
	$smarty->assign('mapId', $info["mapId"]);
	$smarty->assign('name', $info["name"]);
	$smarty->assign('projectName', $mapinfo["projectName"]);
	$smarty->assign('layerGroupId', $info["layerGroupId"]);
	$smarty->assign('islayerGroup', $info["islayerGroup"]);
	$smarty->assign('layerAggregate', $info["layerAggregate"]);
	$smarty->assign('author', $info["author"]);
	$smarty->assign('type', $info["type"]);
	$parentgroup=$layerlib->list_layerGroups($info["mapId"], -1);
	$smarty->assign('copyright', $info["copyright"]);
	$smarty->assign('parentgroup', $parentgroup["data"]);
	$smarty->assign("copyrightUrl", $info["copyrightUrl"]);
	$smarty->assign("gateway", $info["gateway"]);
	$smarty->assign("layerRendering", $info["layerRendering"]);
	$smarty->assign("description", $info["description"]);
} else {
	$smarty->assign('name', '');
	$smarty->assign('mapId', $_REQUEST["mapId"]);
	$map=$layerlib->get_map($_REQUEST["mapId"]);
	$parentgroup=$layerlib->list_layerGroups($_REQUEST["mapId"], -1);
	$smarty->assign('layerId', '');
	$smarty->assign('projectName', $map["projectName"]);
	$smarty->assign('author', $user);
	$smarty->assign('islayerGroup', 1);
	$smarty->assign('layerRendering', '');
	$smarty->assign('layerAggregate', 0);
	$smarty->assign('parentgroup', $parentgroup["data"]);
	$smarty->assign('parent', '');
	$smarty->assign('type', 'POINT');
	$smarty->assign('gateway', '');
	$smarty->assign('description', '');
}



	include_once("categorize_list.php");
// Now assign if the set button was pressed
if (isset($_REQUEST["save"])) {
	check_ticket('edit-layergroup');
	if ($_REQUEST["name"]=='' || $_REQUEST["islayerGroup"] == '' || $_REQUEST["projectName"] == '' || $_REQUEST["layerAggregate"] == '' || $_REQUEST["layerGroupId"] == ''){
		$smarty->assign('msg', tra("Please fill Name, Layer options, ProjectName,  Parent Layer fields from the form"));
	        $smarty->display("error.tpl");
		die;
	}
	$smarty->assign('name', $_REQUEST["name"]);
	$smarty->assign('layerId', $_REQUEST["layerId"]);
	//prepare data array for database
	$data=array();
	$data["name"]=$_REQUEST["name"];
	$data["islayerGroup"]=$_REQUEST["islayerGroup"];
	$data["layerGroupId"]=$_REQUEST["layerGroupId"];
	$data["author"]=$_REQUEST["author"];
	$data["layerRendering"]=$_REQUEST["layerRendering"];
	$data["copyright"]=$_REQUEST["copyright"];
	$data["copyrightUrl"]="tiki-index.php?page=".$_REQUEST["copyright"];
	$data["layerAggregate"]=$_REQUEST["layerAggregate"];
	$data["mapId"]=$_REQUEST["mapId"];
	$data["description"]=$_REQUEST["description"];
	if ($layerId) {
		$data["layerId"]=$_REQUEST["layerId"];
		if(isset($_REQUEST["gateway"])) {
			$data["gateway"]=$_REQUEST["gateway"];
		} else {
			$data["gateway"]='';
		}
		$layerlib->replace_layergroup($layerId,$data);
	} else {
	
		$data["gateway"]='';
		$layerId=$layerlib->add_layergroup($data);
	}
	
	$cat_type = 'layer';
	$cat_objid = $layerId;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["name"];
	$cat_href = "tiki-mapeditor_layers.php?layerId=" . $cat_objid;
	include_once ("categorize.php");
	header ("location: tiki-mapeditor_layers.php?layerId=$layerId");
	die;
}	
$names = $userlib->get_users(0, -1, 'login_desc', '');
$smarty->assign_by_ref('names', $names["data"]);
ask_ticket('edit-layer');

// Display the template
$smarty->assign('mid', 'tiki-mapeditor_edit_layergroup.tpl');
$smarty->display("tiki.tpl");

?>
