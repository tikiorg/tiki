<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if ($feature_trackers == 'y') {
	if (isset($module_params["status"])) {
		$status = $module_params["status"];
	} else {
		$status = '';
	}
	if (isset($module_params["trackerId"]) && isset($module_params["name"])) {
		global $trklib;
		if (!is_object($trklib)) {
			require_once('lib/trackers/trackerlib.php');
		}
		$ranking = $tikilib->list_tracker_items($module_params["trackerId"], 0, $module_rows, 'created_desc', '', $status);
		$smarty->assign('modlifn', $module_params["name"]);
		$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
	} else {

		$ranking = array('data'=>array(
				0 =>array(
					'field_values'=>array(
						0 => array(
						'name' => 'usage',
						'value' => tra('This module requires parameters trackerId and name set')))
					)
				));
		$smarty->assign('modlifn', 'usage');
		$smarty->assign('nonums','y');
	}

	$smarty->assign('modLastItems', $ranking["data"]);
}

?>
