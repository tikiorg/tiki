<?php

// $Header: /cvsroot/tikiwiki/tiki/modules/mod-top_forum_posters.php,v 1.3 2004-08-26 19:24:11 mose Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ('lib/rankings/ranklib.php');
$posters = $ranklib->forums_top_posters(10);

$smarty->assign('modTopForumPosters', $posters["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');

?>
