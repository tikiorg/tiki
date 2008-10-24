<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $ranklib; include_once ('lib/rankings/ranklib.php');

$ranking = $ranklib->forums_ranking_last_posts($module_rows);

$replyprefix = tra("Re:");

if ($ranking) {
	$max=count($ranking["data"]);
	for ($flp = 0; $flp < $max; $flp++) {
	    if (isset($ranking["data"][$flp])) {
		    $name = $ranking["data"][$flp]["name"];
		    $name = str_replace($replyprefix, "", $name);
	    	$ranking["data"][$flp]["name"] = $name;
	    }
	}
}
$smarty->assign('modForumsLastPosts', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
