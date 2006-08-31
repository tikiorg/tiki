<?php

// $Header: /cvsroot/tikiwiki/_mods/modules/top_commenters/mod-top_commenters.php,v 1.1 2006-08-31 12:49:30 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!function_exists("top_commenters_help")) {
	function top_commenters_help() {
		return "type=wiki|article|forum|faq|blog|..., max=>nb, lastdays=>nb, nonums=y, typeChoice=y, lastdaysChoice=y,lastdaysList=-1-2-";
	}
}
if (!function_exists("top_commenters")) {
	/* type = '' for all or 'wiki page', 'forum','faq','blog' .... */
	function top_commenters($limit, $type='', $nbdays='') {
	global $tikilib;
		$mid = "";
		$bindvars = array();
		$retu = array();
		if ($type) {
			$mid .= " and `objectType`=? ";
			if ($type == "wiki")
				$type = "wiki page";
			$bindvars[] = $type;
			if ($type == "forum") {
				$mid .= " and `parentId` != 0 ";
			}
		}
		if ($nbdays) {
			$mid .= " and `commentDate`>=? ";
			$bindvars[] = time() - 60*60*24*$nbdays;
		}
		$query = "select distinct `userName`, count(*) as `nb` from `tiki_comments` where 1 $mid group by `userName` order by ".$tikilib->convert_sortmode("nb_desc")." , `username` asc";
		$result = $tikilib->query($query, $bindvars, $limit);
		while ($res = $result->fetchRow()) {
			$retu[] = $res;
		}

		return $retu;
	}
}
global $nb_mod_top_commenters;
++$nb_mod_top_commenters;

if (isset($_REQUEST["type_commenters".$nb_mod_top_commenters]))
	$type =$_REQUEST["type_commenters".$nb_mod_top_commenters];
elseif (isset($module_params["type"])) {
	$type = $module_params["type"];
}
else
	$type = "";
$smarty->assign('type', $type);

if (isset($_REQUEST["ld_commenters".$nb_mod_top_commenters]))
	$lastdays = $_REQUEST["ld_commenters".$nb_mod_top_commenters];
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

$smarty->assign('typeList', array("wiki page", "forum", "article", "blog", "faq"));
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('typeChoice', isset($module_params["typeChoice"]) ? $module_params["typeChoice"] : '');
$smarty->assign('lastdaysChoice', isset($module_params["lastdaysChoice"]) ? $module_params["lastdaysChoice"] : '');

$commenters = top_commenters($module_rows, $type, $lastdays);
$smarty->assign('module_rows', $module_rows);
$smarty->assign('commenters', $commenters);
$smarty->assign('url', $_SERVER["REQUEST_URI"]);
$smarty->assign('nb_mod_top_commenters', $nb_mod_top_commenters);

?>
