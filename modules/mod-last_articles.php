<?php
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-last_articles.php,v 1.1 2003-10-07 08:51:38 dcengija Exp $
$ranking = $tikilib->list_articles(0,$module_rows,'publishDate_desc', '', date("U"), '', '', '');
$smarty->assign('modLastArticles',$ranking["data"]);
?>
