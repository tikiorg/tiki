<?php
$ranking = $tikilib->list_articles($maxArticles,$maxArticles + $module_rows,'publishDate_desc', '', '');
$smarty->assign('modOldArticles',$ranking["data"]);

?>