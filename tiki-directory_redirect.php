<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/directory/dirlib.php');

if($feature_directory != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_view_directory != 'y') {
  $smarty->assign('msg',tra("Permission denied"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}  


if(!isset($_REQUEST['siteId'])) {
  $smarty->assign('msg',tra("No site indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

$site_info = $dirlib->dir_get_site($_REQUEST['siteId']);
$url = $site_info['url'];
// Add a hit to the site
$dirlib->dir_add_site_hit($_REQUEST['siteId']);

// Redirect to the site URI
header("location: $url");
die;

?>