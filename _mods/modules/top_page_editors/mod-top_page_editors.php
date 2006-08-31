<?php

// $Header: /cvsroot/tikiwiki/_mods/modules/top_page_editors/mod-top_page_editors.php,v 1.1 2006-08-31 12:49:30 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!function_exists("top_page_editors_help")) {
	function top_page_editors_help() {
		return "max=>nb, lastdays=>nb, nonums=y, lastdaysChoice=y, lastdaysList=-1-2-";
	}
}
if (!function_exists("top_page_editors")) {
	function top_page_editors($limit, $nbdays='') {
	global $tikilib;
		$mid = "";
		$bindvars = array();
		$bindvars[] = "%@%";
		$retu = array();
		if ($nbdays) {
			$mid .= " and `lastModif`>=? ";
			$bindvars[] = time() - 60*60*24*$nbdays;
		}
		$query = "select `user`, count(*) as `nb` from `tiki_history` where `user` not like ? $mid group by `user` order by ".$tikilib->convert_sortmode("nb_desc")." , `user` asc";
		$result = $tikilib->query($query, $bindvars, $limit);
		while ($res = $result->fetchRow()) {
			$retu[] = $res;
		}
		$query = "select `user`, count(*) as `nb` from `tiki_pages` where `user` not like ? $mid group by `user` order by ".$tikilib->convert_sortmode("nb_desc")." , `user` asc";
		$result = $tikilib->query($query, $bindvars, $limit);
		while ($res = $result->fetchRow()) {
			for ($i = 0; $i < count($retu); ++$i) {
				if ($retu[$i]['user'] == $res['user'])
					$retu[$i]['nb'] += $res['nb'];
					break;
				}
			if ($i >= count($retu))
				$retu[] = $res;
		}
		usort($retu, 'cmp_editors');
		array_slice($retu, 0, $limit);
		return $retu;
	}
	function cmp_editors($a1, $a2) {
		if ($a1['nb'] == $a2['nb'])
			return  strcmp($a1['user'], $a2['user']);
		else
			return ($a2['nb'] - $a1['nb']);
	}
}
global $nb_mod_top_page_editors;
++$nb_mod_top_page_editors;

if (isset($_REQUEST["ld_page_editors".$nb_mod_top_page_editors]))
	$lastdays = $_REQUEST["ld_page_editors".$nb_mod_top_page_editors];
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

$editors = top_page_editors($module_rows, $lastdays);
$smarty->assign('module_rows', $module_rows);
$smarty->assign('editors', $editors);
$smarty->assign('url', $_SERVER["REQUEST_URI"]);
$smarty->assign('nb_mod_top_page_editors', $nb_mod_top_page_editors);

?>
