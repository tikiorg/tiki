<?php

// $Header: /cvsroot/tikiwiki/_mods/modules/top_edited_pages/mod-top_edited_pages.php,v 1.1 2006-08-31 12:49:30 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!function_exists("top_edited_pages_help")) {
	function top_edited_pages_help() {
		return "max=>nb, lastdays=>nb, nonums=y, lastdaysChoice=y";
	}
}
if (!function_exists("top_edited_pages")) {
	function top_edited_pages($limit, $nbdays='') {
	global $tikilib;
		$mid = "";
		$bindvars = array();
		$retu = array();
		if ($nbdays) {
			$query = "select `pageName`, count(*) as `nb` from `tiki_history` where `lastModif` >= ? group by `pageName` order by ".$tikilib->convert_sortmode("nb_desc")." , `pageName` asc";
			$bindvars[] = time() - 60*60*24*$nbdays;
		}
		else {
			$query = "select `pageName`, `version` as `nb` from `tiki_pages` order by  `version` desc, `pageName` asc";
		}
		$result = $tikilib->query($query, $bindvars, $limit);
		while ($res = $result->fetchRow()) {
			$retu[] = $res;
		}
		return $retu;
	}
}
global $nb_mod_top_edited_pages;
++$nb_mod_top_edited_pages;

if (isset($_REQUEST["ld_edited_pages".$nb_mod_top_edited_pages]))
	$lastdays = $_REQUEST["ld_edited_pages".$nb_mod_top_edited_pages];
elseif (isset($module_params["lastdays"]))
	$lastdays = $module_params["lastdays"];
else
	$lastdays = "";
$smarty->assign('lastdays', $lastdays);

if (isset($module_params["lastdaysList"]))
	$lastdaysList = $module_params["lastdaysList"];
else
	$lastdaysList = array("1", "7", "14", "30", "60");
$smarty->assign('lastdaysList', $lastdaysList);

$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : '');
$smarty->assign('lastdaysChoice', isset($module_params["lastdaysChoice"]) ? $module_params["lastdaysChoice"] : '');

$edited_pages = top_edited_pages($module_rows, $lastdays);
$smarty->assign('module_rows', $module_rows);
$smarty->assign('edited_pages', $edited_pages);
$smarty->assign('url', $_SERVER["REQUEST_URI"]);
$smarty->assign('nb_mod_top_edited_pages', $nb_mod_top_edited_pages);

?>
