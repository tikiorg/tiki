<?php
//$Id$
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
if (!function_exists("mod_article_topics_help")) {
	function mod_article_topics_help() {
		return tra('list topics with links to the articles');
	}
}

$smarty->assign('module_title', isset($module_params["title"]) ? $module_params["title"] : tra("Article Topics"));

global $artlib; include_once('lib/articles/artlib.php');

$listTopics = $artlib->list_topics();
$smarty->assign('listTopics', $listTopics);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
?>
