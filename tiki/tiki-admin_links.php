<?php

require_once('tiki-setup.php');

if($feature_featuredLinks != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

// PERMISSIONS: NEEDS p_admin
if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
  }
}

$smarty->assign('title','');
$smarty->assign('type','f');
$smarty->assign('position',1);

if(isset($_REQUEST["generate"])) {
 $tikilib->generate_featured_links_positions();
}

if(!isset($_REQUEST["editurl"])) {
  $_REQUEST["editurl"]='n';
}
if($_REQUEST["editurl"]!='n') {
  $info = $tikilib->get_featured_link($_REQUEST["editurl"]);
  if(!$info) {
    $smarty->assign('msg',tra("Unexistant link"));
    $smarty->display('error.tpl');
    die;
  }
  $smarty->assign('title',$info["title"]);
  $smarty->assign('position',$info["position"]);
  $smarty->assign('type',$info["type"]);
  
}
$smarty->assign('editurl',$_REQUEST["editurl"]);

if(isset($_REQUEST["add"])) {
  if(!empty($_REQUEST["url"]) && !empty($_REQUEST["url"])) {
    if($_REQUEST["editurl"]==0) {
      $tikilib->add_featured_link($_REQUEST["url"],$_REQUEST["title"],'',$_REQUEST["position"],$_REQUEST["type"]);
    } else {
      $tikilib->update_featured_link($_REQUEST["url"], $_REQUEST["$title"], '', $_REQUEST["position"],$_REQUEST["type"]);
    }
  }
}

if(isset($_REQUEST["remove"])) {
  $tikilib->remove_featured_link($_REQUEST["remove"]);
}

$links=$tikilib->get_featured_links();
$smarty->assign_by_ref('links',$links);


$smarty->assign('mid','tiki-admin_links.tpl');
$smarty->display('tiki.tpl');


?>