<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

if ($feature_trackers == 'y') {
	$smarty->assign('modlifn', $module_params["name"]);
	if (isset($module_params["status"])) {
		$status = $module_params["status"];
	} else {
		$status = '';
	}
	if (isset($module_params["trackerId"])) {
		$ranking = $tikilib->list_tracker_items($module_params["trackerId"], 0, $module_rows, 'created_desc', '', $status);
	} else {
		$ranking = array();
	}

	$smarty->assign('modLastItems', $ranking["data"]);
  $smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
}

?>
