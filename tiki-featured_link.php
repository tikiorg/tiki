<?php

require_once('tiki-setup.php');

// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["url"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display('error.tpl');
  die;
}


$smarty->assign_by_ref('url',$_REQUEST["url"]);
$smarty->assign('mid','tiki-featured_link.tpl');
$smarty->display('tiki.tpl');


?>