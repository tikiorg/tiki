<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/wiki/histlib.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


// Only an admin can use this script
if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
  }
}

// We have to get the variable ruser as the user to check
if(!isset($_REQUEST["ruser"])) {
  $smarty->assign('msg',tra("No user indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if(!user_exists($_REQUEST["ruser"])) {
  $smarty->assign('msg',tra("Unexistant user"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

$smarty->assign_by_ref('ruser',$_REQUEST["ruser"]);
$smarty->assign('preview',false);

if(isset($_REQUEST["preview"])) {
  $version = $histlib->get_version($_REQUEST["page"],$_REQUEST["version"]);
  $version["data"] = $tikilib->parse_data($version["data"]);
  if($version) {
    $smarty->assign_by_ref('preview',$version);
    $smarty->assign_by_ref('version',$_REQUEST["version"]); 
  } 
}

$history = $histlib->get_user_versions($_REQUEST["ruser"]);
$smarty->assign_by_ref('history',$history);

$smarty->assign('mid','tiki-userversions.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>