<?php
require_once('tiki-setup.php');
include_once('lib/featured_links/flinkslib.php');

if($feature_featuredLinks != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$flinkslib->add_featured_link_hit($_REQUEST["url"]);
// Get the page from the request var or default it to HomePage


if(!isset($_REQUEST["url"])) {
  $smarty->assign('msg',tra("No page indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

$section='featured_links';
include_once('tiki-section_options.php');


$smarty->assign_by_ref('url',$_REQUEST["url"]);
$smarty->assign('mid','tiki-featured_link.tpl');
$smarty->display("styles/$style_base/tiki.tpl");


?>