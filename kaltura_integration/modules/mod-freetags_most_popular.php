<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $prefs, $tiki_p_view_freetags, $smarty;
if ($prefs['feature_freetags'] != 'y') {
	$smarty->assign('module_error', tra("This feature is disabled").": feature_freetags");
} elseif ($tiki_p_view_freetags != 'y') {
	$smarty->assign('module_error', tra("You do not have permission to use this feature"));
} else {
	global $freetaglib; require_once 'lib/freetag/freetaglib.php';
	$most_popular_tags = $freetaglib->get_most_popular_tags('', 0, empty($module_params['max'])?$module_rows: $module_params['max']);
	$smarty->assign_by_ref('most_popular_tags', $most_popular_tags);
	$smarty->assign('type', (isset($module_params['type']) && $module_params['type'] =='cloud') ? 'cloud' : 'list');
}