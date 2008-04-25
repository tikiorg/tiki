<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!function_exists('mod_forums_last_topics_help')) {
        function mod_forums_last_topics_help() {
                return 'forumId=id1:id2,lastreplied=y|n';
        }
}

// Parameter absurl set if the last_topics url is absolute or not [y|n].
// If not set, default = relative

global $ranklib; include_once ('lib/rankings/ranklib.php');

if (isset($module_params['lastreplied']) && $module_params['lastreplied'] == 'y') {
	$lastreplied = true;	
} else {
	$lastreplied = false;	
}

if (isset($module_params['forumId'])) {
	if (strstr($module_params['forumId'], ':')) {
		$ranking = $ranklib->forums_ranking_last_topics($module_rows, explode(':',$module_params['forumId']), $lastreplied);
	} else {
		$ranking = $ranklib->forums_ranking_last_topics($module_rows, $module_params['forumId'], $lastreplied);
	}
} else {	
	$ranking = $ranklib->forums_ranking_last_topics($module_rows, '', $lastreplied);
}
$smarty->assign('modForumsLastTopics', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('absurl', isset($module_params["absurl"]) ? $module_params["absurl"] : 'n');
?>
