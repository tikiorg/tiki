<?php
// $Header: /cvsroot/tikiwiki/tiki/modules/mod-last_articles.php,v 1.4 2004-03-27 21:24:50 mose Exp $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

$ranking = $tikilib->list_articles(0,$module_rows,'publishDate_desc', '', date("U"), '', '', '', 'y');
$smarty->assign('modLastArticles',$ranking["data"]);
$smarty->assign('nonums', isset($module_params["nonums"]) ? $module_params["nonums"] : 'n');
?>
