<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

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
$ranking = $tikilib->list_articles($start, $module_rows, 'publishDate_desc', '', '', $user, $type, $topicId, 'y', $topic);

$smarty->assign('modArticles', $ranking["data"]);

?>
