<?php
// Initialization
require_once('tiki-setup.php');


if($feature_wiki_multiprint != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

// Now check permissions if user can view wiki pages
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


if(!isset($_REQUEST["printpages"])) {
  $printpages = Array();
} else {
  $printpages = unserialize(urldecode($_REQUEST["printpages"]));
}

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];
} else {
  $find = '';
}
$smarty->assign('find',$find);

if(isset($_REQUEST["addpage"])) {
  if(!in_array($_REQUEST["pageName"],$printpages)) {
    $printpages[] = $_REQUEST["pageName"];
  }
}
if(isset($_REQUEST["clearpages"])) {
  $printpages = Array();
}


$smarty->assign('printpages',$printpages);
$form_printpages = urlencode(serialize($printpages));
$smarty->assign('form_printpages',$form_printpages);



$pages = $tikilib->list_pages(0, -1,  'pageName_asc',$find);
$smarty->assign_by_ref('pages',$pages["data"]);

// Display the template
$smarty->assign('mid','tiki-print_pages.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
