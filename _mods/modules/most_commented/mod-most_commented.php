<?php

// $Header: /cvsroot/tikiwiki/_mods/modules/most_commented/mod-most_commented.php,v 1.1 2006-08-31 12:49:30 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!function_exists("most_commented_help")) {
	function most_commented_help() {
		return "type=wiki|wiki page|article|forum topic|blog|faq|..., max=>nb, lastdays=>nb, nonums=y, typeChoice=y, lastdaysChoice=y, lastdaysList=-1-2-";
	}
}
if (!function_exists("most_commented")) {
	/* type = '' for all or 'wiki page', 'forum','faq','blog' .... */
	function most_commented($limit, $type='', $nbdays='') {
	global $tikilib;
		$mid = "";
		$bindvars = array();
		$retu = array();
		if ($type) {
			$mid .= " and `objectType`=? ";
			if ($type == "wiki")
				$bindvars[] = "wiki page";
			else if ($type == "forum topic") {
				$mid .= " and `parentId` != 0 ";
				$bindvars[] = "forum";
			}
			else
				$bindvars[] = $type;
		}
		if ($nbdays) {
			$mid .= " and `commentDate`>=? ";
			$bindvars[] = time() - 60*60*24*$nbdays;
		}
		$query = "select  *, count(*) as `nb` from `tiki_comments` where 1 $mid group by `object`, `objectType` order by ".$tikilib->convert_sortmode("nb_desc").", `hits`desc";
		$result = $tikilib->query($query, $bindvars, $limit);
		while ($res = $result->fetchRow()) {
			if ($res['objectType'] == "article") {
				$query = "select * from `tiki_articles` where `articleId`= ?";
				$result2 = $tikilib->query($query, array($res['object']));
				if ($res2 = $result2->fetchRow()) {
					$res2['nb'] = $res['nb'];
					$res2['objectType'] = $res['objectType'];
					$retu[] = $res2;
				}
				else
					$retu[] = $res;
			}
			elseif ($res['objectType'] == "forum") {
				$query = "select * from `tiki_comments` where `threadId`= ?";
				$result2 = $tikilib->query($query, array($res['parentId']));
				if ($res2 = $result2->fetchRow()) {
					$res2['nb'] = $res['nb'];
					$retu[] = $res2;
				}
				else
					$retu[] = $res;
			}
			elseif ($res['objectType'] == "faq") {
				$query = "select * from `tiki_faqs` where `faqId`= ?";
				$result2 = $tikilib->query($query, array($res['object']));
				if ($res2 = $result2->fetchRow()) {
					$res2['nb'] = $res['nb'];
					$res2['objectType'] = $res['objectType'];
					$retu[] = $res2;
				}
				else
					$retu[] = $res;
			}
			elseif ($res['objectType'] == "blog") {
				$query = "select * from `tiki_blogs` where `blogId`= ?";
				$result2 = $tikilib->query($query, array($res['object']));
				if ($res2 = $result2->fetchRow()) {
					$res2['nb'] = $res['nb'];
					$res2['objectType'] = $res['objectType'];
					$retu[] = $res2;
				}
				else
					$retu[] = $res;
			}
			else
				$retu[] = $res;
		}
//echo "<pre>";print_r($retu);echo "</pre>";
		return $retu;
	}
}
global $nb_mod_most_commented;
++$nb_mod_most_commented;

if (isset($_REQUEST["type_commented".$nb_mod_most_commented]))
	$type =$_REQUEST["type_commented".$nb_mod_most_commented];
elseif (isset($module_params["type"])) {
	$type = $module_params["type"];
	if ($type == "wiki")
		$type = "wiki page";
}
else
	$type = "";
$smarty->assign('type', $type);

if (isset($_REQUEST["ld_commented".$nb_mod_most_commented]))
	$lastdays = $_REQUEST["ld_commented".$nb_mod_most_commented];
elseif (isset($module_params["lastdays"]))
	$lastdays = $module_params["lastdays"];
else
	$lastdays = "";
$smarty->assign('lastdays', $lastdays);
if (isset($module_params["lastdaysList"])) { // separator is the first char
	$lastdaysList = split(substr($module_params["lastdaysList"], 1, 1), $module_params["lastdaysList"]);
}
else
	$lastdaysList = array("1", "7", "14", "30", "60");
$smarty->assign('lastdaysList', $lastdaysList);

$smarty->assign('typeList', array("wiki page", "forum topic", "article", "blog", "faq"));
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('typeChoice', isset($module_params["typeChoice"]) ? $module_params["typeChoice"] : '');
$smarty->assign('lastdaysChoice', isset($module_params["lastdaysChoice"]) ? $module_params["lastdaysChoice"] : '');

$comments = most_commented($module_rows, $type, $lastdays);
$smarty->assign('module_rows', $module_rows);
$smarty->assign('comments', $comments);
$smarty->assign('url', $_SERVER["REQUEST_URI"]);
$smarty->assign('nb_mod_most_commented', $nb_mod_most_commented);

?>
