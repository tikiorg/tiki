<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// in wikipages, add params like this:    ,trackerId=...,name=...
// in module list, add params like this in params fiels:  trackerId=...&name=...
// name is the name of the tracker field to be displayed (should be descriptive)
global $prefs;

if ($prefs['feature_trackers'] == 'y') {
	if (isset($module_params["status"])) {
		$status = $module_params["status"];
	} else {
		$status = '';
	}
	$modLastItems = array();
	if (isset($module_params["trackerId"]) && isset($module_params["name"])) {
		$tmp = $tikilib->list_tracker_items($module_params["trackerId"], 0, $module_rows, 'created_desc', '','','', $status);
		foreach ($tmp["data"] as $data) {
			foreach ($data["field_values"] as $data2) {
				if (isset($data2["name"])) {
					if (strtolower($data2["name"])==strtolower($module_params["name"])) {
						$data["subject"] = $data2["value"];
						break; // found a subject
					}
				}
			}
			$data["id"]=$module_params["trackerId"];
			$data["field_values"]=null;
			$modLastItems[] = $data;
			$data=null;
		}
		$tmp=null;
		$smarty->assign('modlifn', $module_params["name"]);
  		$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
		$smarty->assign('modLastItems', $modLastItems);
	} else {
		$smarty->assign('module_error', tra('This module requires parameters trackerId and name set'));
    }

}

?>
