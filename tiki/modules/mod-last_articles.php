<?php
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-last_articles.php,v 1.3 2004-03-15 21:27:33 mose Exp $
$ranking = $tikilib->list_articles(0,$module_rows,'publishDate_desc', '', date("U"), '', '', '', 'y');
$smarty->assign('modLastArticles',$ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
?>
