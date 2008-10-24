<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!isset($prefs['maxArticles']))
	$prefs['maxArticles'] = 0;

$ranking = $tikilib->list_articles($prefs['maxArticles'], $module_rows, 'publishDate_desc', '', '', $user);
$smarty->assign('modOldArticles', $ranking["data"]);

?>
