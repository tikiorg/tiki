<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!isset($maxArticles))
	$maxArticles = 0;

$ranking = $tikilib->list_articles($maxArticles, $module_rows, 'publishDate_desc', '', '', $user);
$smarty->assign('modOldArticles', $ranking["data"]);

?>
