<?php
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-last_articles.php,v 1.2 2003-10-20 01:13:43 zaufi Exp $
$ranking = $tikilib->list_articles(0,$module_rows,'publishDate_desc', '', date("U"), '', '', '');
$smarty->assign('modLastArticles',$ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
?>
