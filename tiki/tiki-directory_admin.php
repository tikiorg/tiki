<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/directory/dirlib.php');

if($feature_directory != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

// This will only display a menu to
// admin_categories
// admin_sites
// validate_sites
// configuration (in tiki admin if admin)
// 

// Get number of invalid sites
// Get number of sites
// Get number of categories
// Get number of searches
$stats = $dirlib->dir_stats();
$smarty->assign_by_ref('stats',$stats);

// Display the template
$smarty->assign('mid','tiki-directory_admin.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>