<?php
if (!isset($maxArticles)) $maxArticles = 0;
$ranking = $tikilib->list_articles($maxArticles,$maxArticles + $module_rows,'publishDate_desc', '', '',$user);
$smarty->assign('modOldArticles',$ranking["data"]);
?>
