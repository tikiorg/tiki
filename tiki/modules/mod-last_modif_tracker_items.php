<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

if ($feature_trackers == 'y') {
	$smarty->assign('modlmifn', $module_params["name"]);

	if (isset($module_params["trackerId"])) {
		$ranking = $tikilib->list_tracker_items($module_params["trackerId"], 0, $module_rows, 'lastModif_desc', '');
	} else {
		$ranking = array();
		$ranking['data'] = '';
	}

	$smarty->assign('modLastModifItems', $ranking["data"]);
    $smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
}

?>
