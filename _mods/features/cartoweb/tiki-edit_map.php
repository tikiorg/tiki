<?php
// Initialization
require_once ('tiki-setup.php');

require_once ('lib/tikilib.php'); # httpScheme()
include_once ('lib/map/layer.php');

if (!isset($layerlib)) {
	$layerlib = new LayerLib($dbTiki,$dbTiki2);
}

// CHECK FEATURE MAPS AND ADMIN PERMISSION HERE
if ($feature_maps != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_maps");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_map_edit != 'y') {
	$smarty->assign('msg', tra("You do not have permissions to edit maps"));

	$smarty->display("error.tpl");
	die;
}

	if (isset($_REQUEST["cat_restrict_child"])) {
		$cat_restrict_child = $_REQUEST["cat_restrict_child"];
	} else {
		$cat_restrict_child = 0;
	}
	if (isset($_REQUEST["mapId"])) {
		$mapId=$_REQUEST["mapId"];
		$cat_objid = $_REQUEST["mapId"];
		} else {
		$mapId='';
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
		$cat_type ="map";
	}		
	$smarty->assign('individual', 'n');
		include_once ('lib/categories/categlib.php');
		$categories=$categlib->get_object_categories('map', $mapId);
		if($categories) {
		$gateways=$categlib->list_category_objects($categories[0],0,$maxRecords,'','wiki page');
		$smarty->assign('gateways', $gateways['data']);
		}
	if ($userlib->object_has_one_permission($mapId, 'map')) {
		$smarty->assign('individual', 'y');
		if ($tiki_p_admin != 'y') {
		// Now get all the permissions that are set for this type of permissions 'map'
		$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'map');

		foreach ($perms["data"] as $perm) {
			$permName = $perm["permName"];

			if ($userlib->object_has_permission($user, $mapId, 'map', $permName)) {
				$$permName = 'y';

				$smarty->assign("$permName", 'y');
			} else {
				$$permName = 'n';

				$smarty->assign("$permName", 'n');
			}
		}
	}
}

if (isset($_REQUEST["mapId"]) && $_REQUEST["mapId"] > 0) {
	$info = $layerlib->get_map($_REQUEST["mapId"]);
	$mapId=$info["mapId"];
	if (!$info) {
		$smarty->assign('msg', tra("Map not found"));

		$smarty->display("error.tpl");
		die;
	}

	// Check user is admin or the name
	if (($user != $info["author"]) && ($tiki_p_map_edit != 'y')) {
		$smarty->assign('msg', tra("You do not have permission to edit this map"));

		$smarty->display("error.tpl");
		die;
	}
	
	$smarty->assign('mapId', $mapId);
	$smarty->assign('name', $info["name"]);
	$smarty->assign('projectName', $info["projectName"]);
	$smarty->assign('db', $info["db"]);
	$smarty->assign('author', $info["author"]);
	$smarty->assign('type', $info["type"]);
	$smarty->assign('path', $info["path"]);
	$smarty->assign('copyright', $info["copyright"]);
	$smarty->assign('copyrightUrl', $info["copyrightUrl"]);
	$smarty->assign('gateway', $info["gateway"]);
	$smarty->assign('description', $info["description"]);
	$smarty->assign('cat_type', $cat_type);
	$smarty->assign('cat_objid', $cat_objid);
} else {
	$smarty->assign('name', '');
	$smarty->assign('mapId', '');
	$smarty->assign('projectName', $_ENV['CW3_PROJECT']);
	$smarty->assign('db', '');
	$smarty->assign('author', $user);
	$smarty->assign('type', 'cartoweb');
	$smarty->assign('path', '');
	$smarty->assign('description', '');
	$smarty->assign('cat_type', '');
	$smarty->assign('cat_objid', '');
}
	


	include_once("categorize_list.php");

// Now assign if the set button was pressed
if (isset($_REQUEST["save"])) {
	check_ticket('edit-map');
	$mapId=$_REQUEST["mapId"];
	$smarty->assign('name', $_REQUEST["name"]);
	$smarty->assign('path', $_REQUEST["path"]);
	$smarty->assign('type', $_REQUEST["type"]);
	$smarty->assign('projectName', $_REQUEST["projectName"]);
	$smarty->assign('db', $_REQUEST["db"]);
	$smarty->assign('mapId', $_REQUEST["mapId"]);
	$smarty->assign('gateway', $_REQUEST["gateway"]);
	$smarty->assign('description', $_REQUEST["description"]);
	// check post data 
	
	if ($_REQUEST["name"]=='' || $_REQUEST["path"] == '' || $_REQUEST["projectName"] == '' || $_REQUEST["db"] == '' ){
		$smarty->assign('msg', tra("Please fill Name, Path, ProjectName, Database fields from the form"));
		$smarty->display("error.tpl");
		die;
	}
	
	//prepare data array for database
	$data=array();
	$data["name"]=$_REQUEST["name"];
	$data["projectName"]=$_REQUEST["projectName"];
	$data["author"]=$_REQUEST["author"];
	$data["type"]=$_REQUEST["type"];
	$data["path"]=$_REQUEST["path"];
	$data["copyright"]=$_REQUEST["copyright"];
	$data["copyrightUrl"]="tiki-index.php?page=".$_REQUEST["copyright"];
	$data["db"]=$_REQUEST["db"];
	$data["description"]=$_REQUEST["description"];

	if ($mapId) {
	$data["mapId"]=$_REQUEST["mapId"];
	$data["gateway"]=$_REQUEST["gateway"];
	$layerlib->replace_map($mapId,$data);
	} else {
	$data["gateway"]='';
	$mapId=$layerlib->add_map($data);
	}
	$cat_type = 'map';
	$cat_objid = $mapId;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["name"];
	$cat_href = "tiki-list_maps.php?mapId=" . $cat_objid;
	include_once ("categorize.php");
	header ("location: tiki-list_maps.php?mapId=$mapId");
	die;

	

}

$names = $userlib->get_users(0, -1, 'login_desc', '');
$smarty->assign_by_ref('names', $names["data"]);
ask_ticket('edit-map');

// Display the template
$smarty->assign('mid', 'tiki-edit_map.tpl');
$smarty->display("tiki.tpl");

?>
