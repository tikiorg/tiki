<?php

include_once ('lib/rankings/ranklib.php');

$ranking = $ranklib->forums_ranking_last_posts($module_rows);

$replyprefix = tra("Re:");

if ($ranking) {
	for ($i = 0; $i < count($ranking["data"]); $i++) {
	    $name = $ranking["data"][$i]["name"];
	    $name = str_replace($replyprefix, "", $name);
	    $ranking["data"][$i]["name"] = $name;
	}
}
$smarty->assign('modForumsLastPosts', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>