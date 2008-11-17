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

	$ranking = array();
	$ranking['data'] = array();

	if (isset($module_params["trackerId"])) $trackerId = $module_params["trackerId"];
	else $trackerId = 0;
	
	if (isset($module_params["itemId"])) $itemId = $module_params["itemId"];
	else $itemId = 0;

	global $trklib;
	include_once ('lib/trackers/trackerlib.php');
		
	$tmp = $trklib->list_last_comments($trackerId, $itemId, 0, $module_rows);
	foreach ($tmp["data"] as $data) {
	    $data['title'] = strtolower($data["title"]);
	    $ranking['data'][] = $data;
	}
	
	$tmp=null;
	
	
	$smarty->assign('modLastModifComments', $ranking["data"]);
	$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
	$smarty->assign('count', (int) $tmp['cant']);
}

?>
