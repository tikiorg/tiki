<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

$smarty->assign('title', isset($module_params["title"]) ? $module_params["title"] : 'Articles');

if (isset($module_params["type"])) {
	$type = $module_params["type"];
} else {
	$type = '';
}
if (isset($module_params["topicId"])) {
	$topicId = $module_params["topicId"];
} else {
	$topicId = '';
}
/*
$smarty->assign('type', isset($module_params["type"]) ? $module_params["type"] : '');
$smarty->assign('topicId', isset($module_params["topicId"]) ? $module_params["topicId"] : '');

function list_articles($offset = 0, $maxRecords = -1, $sort_mode = 'publishDate_desc', $find = '', $date = '', $user, $type = '', $topicId = '') {
*/

$ranking = $tikilib->list_articles(0, $module_rows, 'publishDate_desc', '', '', $user, $type, $topicId);
//$ranking = $tikilib->list_articles(0, $module_rows, 'reads_desc', '', '', $user);

$smarty->assign('modArticles', $ranking["data"]);

?>
