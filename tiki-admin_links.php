<?php

require_once('tiki-setup.php');

// PERMISSIONS: NEEDS p_admin
if($user != 'admin') {
  if($tiki_p_admin != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
  }
}

if(isset($_REQUEST["add"])) {
  if(!empty($_REQUEST["url"]) && !empty($_REQUEST["url"])) {
    $tikilib->add_featured_link($_REQUEST["url"],$_REQUEST["title"]);
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