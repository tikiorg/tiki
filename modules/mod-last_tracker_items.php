<?php

if ($feature_trackers == 'y') {
	$smarty->assign('modlifn', $module_params["name"]);
	if (isset($module_params["status"])) {
		$status = $module_params["status"];
	} else {
		$status = '';
	}
	if (isset($module_params["trackerId"])) {
		$ranking = $tikilib->list_tracker_items($module_params["trackerId"], 0, $module_rows, 'created_desc', $status);
	} else {
		$ranking = array();
	}

	$smarty->assign('modLastItems', $ranking["data"]);
  $smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
}

?>
