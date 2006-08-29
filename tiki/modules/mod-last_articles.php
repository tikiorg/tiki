<?php
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-last_articles.php,v 1.10 2006-08-29 20:19:11 sylvieg Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// Parameter absurl set if the last_article url is absolute or not [y|n].
// If not set, default = relative

// filter for type, topicId or topic...
// if ! is in front of type or topic, the result is inversed
$mod_type = isset($module_params["type"]) ? $module_params["type"] : '';
$mod_topicId = isset($module_params["topicId"]) ? $module_params["topicId"] : '';
$mod_topic = isset($module_params["topic"]) ? $module_params["topic"] : '';
$smarty->assign('type',$mod_type);
$smarty->assign('topicId',$mod_topicId);

$ranking = $tikilib->list_articles(0,$module_rows,'publishDate_desc', '', date("U"), '', $mod_type, $mod_topicId, 'y', $mod_topic);
$smarty->assign('modLastArticles',$ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
$smarty->assign('absurl', isset($module_params["absurl"]) ? $module_params["absurl"] : 'n');
$module_rows = count($ranking["data"]);
$smarty->assign('module_rows', $module_rows);
?>
