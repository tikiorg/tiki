<?php
$ranking = $tikilib->list_articles(0,$module_rows,'reads_desc', '', '');
$smarty->assign('modTopArticles',$ranking["data"]);
?>