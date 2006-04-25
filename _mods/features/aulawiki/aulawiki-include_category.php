<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

global $tikilib, $cache_time, $smarty, $dbTiki, $feature_categories, $tiki_p_view_categories, $user;

//$feature_directory, $ranklib, $feature_trackers, $tikidomain, $user,$feature_tasks, $feature_user_bookmarks, $tiki_p_tasks, $tiki_p_create_bookmarks, $imagegallib, $language;

$pagename = $smarty->get_template_vars("page");
include_once ('lib/aulawiki/categutillib.php');
$categUtil = new CategUtilLib($dbTiki);
$wpsCateg = $categUtil->get_category_by_name(0, "wikipagescategories");
$wpsCategId = "";
if (!isset ($wpsCateg["categId"])) {
	$wpsCategId = $categUtil->create_object("wikipagescategories", "wiki pages categories", "category", 0);
} else {
	$wpsCategId = $wpsCateg["categId"];
}

$wpCateg = $categUtil->get_category_by_name($wpsCategId, $pagename);
$wpCategId = "";
if (!isset ($wpCateg["categId"])) {
	$wpCategId = $categUtil->create_object($pagename, $pagename." category", "category", $wpsCategId);
} else {
	$wpCategId = $wpCateg["categId"];
}

$module_params = array ();
$module_params["categId"] = $wpCategId;

$out = '';
if (!isset ($align)) {
	$align = 'nofloat';
}

if (!isset ($max)) {
	if (!isset ($rows)) {
		$max = 10; // default value
	} else
		$max = $rows; // rows=> used instead of max=> ?
}

if (!isset ($np)) {
	$np = '1';
}

if (!isset ($args)) {
	$args = '';
}

$phpfile = 'modules/mod-aulawiki_resources.php';
$template = 'modules/mod-aulawiki_resources.tpl';

$module_rows = $max;
$smarty->assign_by_ref('module_rows', $module_rows);
$smarty->assign_by_ref('module_params', $module_params); // module code can unassign this if it wants to hide params

if (file_exists($phpfile)) {
	include ($phpfile);
}

$template_file = 'templates/'.$template;
$smarty->assign('no_module_controls', 'y');
if (file_exists($template_file)) {
	$out = $smarty->fetch($template);
} else {
	if ($tikilib->is_user_module($module)) {
		$info = $tikilib->get_user_module($module);

		$smarty->assign_by_ref('user_title', $info["title"]);
		$smarty->assign_by_ref('user_data', $info["data"]);
		$out = $smarty->fetch('modules/user_module.tpl');
	}
}
$smarty->clear_assign('module_params'); 
$smarty->clear_assign('no_module_controls');
$out = eregi_replace("/n", "", $out);

if ($out) {
	if ($align != 'nofloat') {
		$data = "<div style='float:$align;'>";
	} else {
		$data = "<div>";
	}
	if ($np) {
		$data .= "$out</div>";
	} else {
		$data .= "$out</div>";
	}
} else {
	$data = "<div style='float:$align;color:#AA2200;'>".tra("Sorry no such module")."<br /><b>$module</b></div>".$data;
}

echo $data;
?>