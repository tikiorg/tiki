<?php
// Initialization
require_once('tiki-setup.php');

if($feature_html_pages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


if($tiki_p_view_html_pages != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}

if(!isset($_REQUEST["pageName"])) {
    $smarty->assign('msg',tra("No page indicated"));
    $smarty->display('error.tpl');
    die;
}

$page_data = $tikilib->get_html_page($_REQUEST["pageName"]);
$smarty->assign('type',$page_data["type"]);
$smarty->assign('refresh',$page_data["refresh"]);
$smarty->assign('pageName',$_REQUEST["pageName"]);
$parsed=$tikilib->parse_html_page($_REQUEST["pageName"],$page_data["content"]);
$smarty->assign_by_ref('parsed',$parsed);

$section='html_pages';
include_once('tiki-section_options.php');


// Display the template
$smarty->assign('mid','tiki-page.tpl');
$smarty->display('tiki.tpl');
?>