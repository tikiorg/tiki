<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

if (!isset($maxArticles))
	$maxArticles = 0;

$ranking = $tikilib->list_articles($maxArticles, $maxArticles + $module_rows, 'publishDate_desc', '', '', $user);
$smarty->assign('modOldArticles', $ranking["data"]);

?>
