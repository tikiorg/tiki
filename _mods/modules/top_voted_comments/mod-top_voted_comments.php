<?php

// $Header: /cvsroot/tikiwiki/_mods/modules/top_voted_comments/mod-top_voted_comments.php,v 1.1 2006-08-31 12:49:30 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!function_exists("top_voted_comments_help")) {
	function top_voted_comments_help() {
		return "type=wiki page|article|forum|blog|faq|..., max=>nb, lastdays=>nb, nonums=y, typeChoice=y, lastdaysChoice=y, sort=points|votes|average, lastdaysList=-1-2-, showVotes=n|y, minVotes=..., round=2|3|4";
	}
}
if (!function_exists("top_voted_comments")) {
	/* type = '' for all or 'wiki page', 'forum','faq','blog' .... */
	function top_voted_comments($limit, $type='', $nbdays='', $sort='', $minVotes='', $round=2) {
	global $tikilib;
		$mid = "";
		$bindvars = array();
		$retu = array();
		if ($type) {
			$mid .= " and `objectType`=? ";
			if ($type == "wiki")
				$type = "wiki page";
			$bindvars[] = $type;
			// if you don't want topics : if ($type == "forum") $mid .= " and `parentId` != 0 ";
		}
		if ($nbdays) {
			$mid .= " and `commentDate`>=? ";
			$bindvars[] = time() - 60*60*24*$nbdays;
		}
		if ($minVotes) {
			$mid .= " and votes >=? ";
			$bindvars[] = (int)$minVotes;
		}
		if ($sort == "votes")
			$mid2 = "`votes`";
		elseif ($sort == "points")
			$mid2 = "`points`";
		else
			$mid2 = "`average` desc, `votes`";

		$query = "select * from `tiki_comments` where `votes` !=  0 $mid order by $mid2 desc";
		$result = $tikilib->query($query, $bindvars, $limit);
		while ($res = $result->fetchRow()) {
			$res['nb'] =  ($sort == "votes")? $res['votes'] : (($sort == "points")? $res['points']: sprintf("%.".$round."f",round($res['average'], $round)));
			$retu[] = $res;
		}
		return $retu;
	}
}
global $nb_mod_top_voted_comments;
++$nb_mod_top_voted_comments;

if (isset($_REQUEST["type_voted_comments".$nb_mod_top_voted_comments]))
	$type =$_REQUEST["type_voted_comments".$nb_mod_top_voted_comments];
elseif (isset($module_params["type"])) {
	$type = $module_params["type"];
	if ($type == "wiki")
		$type = "wiki page";
	elseif ($type == "all")
		$type = '';
}
else
	$type = "";
$smarty->assign('type', $type);

if (isset($_REQUEST["ld_voted_comments".$nb_mod_top_voted_comments]))
	$lastdays = $_REQUEST["ld_voted_comments".$nb_mod_top_voted_comments];
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

if (isset($_REQUEST["sort_voted_comments".$nb_mod_top_voted_comments]))
	$sort = $_REQUEST["sort_voted_comments".$nb_mod_top_voted_comments];
elseif (isset($module_params["sort"]))
	$sort = $module_params["sort"];
else
	$sort = "";
$smarty->assign('sort', $sort);

if (isset($module_params["minVotes"]))
	$minVotes = $module_params["minVotes"];
else
	$minVotes = '';

if (isset($module_params["round"]))
	$round = $module_params["round"];
else
	$round = 2;

$smarty->assign('typeList', array("wiki page", "forum", "article", "blog", "faq"));
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('typeChoice', isset($module_params["typeChoice"]) ? $module_params["typeChoice"] : '');
$smarty->assign('lastdaysChoice', isset($module_params["lastdaysChoice"]) ? $module_params["lastdaysChoice"] : '');
$smarty->assign('sortChoice', isset($module_params["sortChoice"]) ? $module_params["sortChoice"] : '');
$smarty->assign('showVotes', isset($module_params["showVotes"]) ? $module_params["showVotes"] : '');

$voted_comments = top_voted_comments($module_rows, $type, $lastdays, $sort, $minVotes, $round);
$smarty->assign('module_rows', $module_rows);
$smarty->assign('voted_comments', $voted_comments);
$smarty->assign('url', $_SERVER["REQUEST_URI"]);
$smarty->assign('nb_mod_top_voted_comments', $nb_mod_top_voted_comments);

?>
