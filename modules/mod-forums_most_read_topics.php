<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $ranklib; include_once ('lib/rankings/ranklib.php');

if (isset($module_params['forumId'])) {
	if (strstr($module_params['forumId'], ':')) {
		$forumId = explode(':',$module_params['forumId']);
	} else {
		$forumId = $module_params['forumId'];
	}
} else {
	$forumId = '';
}

$ranking = $ranklib->forums_ranking_most_read_topics($module_rows, $forumId);
$smarty->assign('modForumsMostReadTopics', $ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
