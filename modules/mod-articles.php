<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
global $smarty, $tikilib, $user, $module_rows;

$smarty->assign('module_title', isset($module_params["title"]) ? $module_params["title"] : tra("Articles"));

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
if (isset($module_params["topic"])) {
	$topic = $module_params["topic"];
} else {
	$topic = '';
}
if (isset($module_params["start"])) {
	$start = $module_params["start"];
} else {
	$start = isset($start) ? $start : 0;
}
if (isset($module_params['sort'])) {
	$sort = $module_params['sort'];
} else {
	$sort = 'publishDate_desc';
}
if (isset($module_params['lang'])) {
	$lang = $module_params['lang'];
} else {
	$lang = '';
}
if (isset($module_params['categId'])) {
	$categId = $module_params['categId'];
} else {
	$categId = '';
}

$ranking = $tikilib->list_articles($start, $module_rows, 'publishDate_desc', '', '', $user, $type, $topicId, 'y', $topic, $categId, '', '', $lang);

$smarty->assign('modArticles', $ranking["data"]);

?>
