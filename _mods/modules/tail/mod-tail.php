<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

$smarty->assign('feature_tail', 'y');

include_once ("lib/tail/taillib.php");

if (!isset($module_params["file"])or !$module_params["file"] or !is_file($module_params["file"])) {
	$tail[] = tra("no such file"). " " .@$module_params["file"];

	$module_params["title"] = tra("Error"). " !";
} else {
	if (!isset($module_params["max"])or !$module_params["max"]) {
		$module_params["max"] = 10;
	}

	if (!isset($module_params["title"])or !$module_params["title"]) {
		$module_params["title"] = "tail";
	}
	
	$reverse = isset($module_params["reverse"]) ? $module_params["reverse"] : 'y';
	if ($reverse == 'n') {
		$tail = tail_read($module_params["file"], $module_params["max"]);
	} else {
		$tail = array_reverse(tail_read($module_params["file"], $module_params["max"]));
	}

	if (isset($module_params["filter"])and $module_params["filter"] and function_exists("tail_filter_" . $module_params["filter"]))
		{
		array_walk($tail, "tail_filter_" . $module_params["filter"]);
	}
}

$smarty->assign('tailtitle', $module_params["title"]);
$smarty->assign('tail', $tail);

?>
