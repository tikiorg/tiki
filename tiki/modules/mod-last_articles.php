<?php
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-last_articles.php,v 1.8 2005-05-18 11:02:28 mose Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

// filter for type, topicId or topic...
// if ! is in front of type or topic, the result is inversed
$mod_type = isset($module_params["type"]) ? $module_params["type"] : '';
$mod_topicId = isset($module_params["topicId"]) ? $module_params["topicId"] : '';
$mod_topic = isset($module_params["topic"]) ? $module_params["topic"] : '';

$ranking = $tikilib->list_articles(0,$module_rows,'publishDate_desc', '', date("U"), '', $mod_type, $mod_topicId, 'y', $mod_topic);
$smarty->assign('modLastArticles',$ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
?>
