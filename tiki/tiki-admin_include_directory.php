<?php

if(isset($_REQUEST["directory"])) {
  if(isset($_REQUEST["directory_validate_urls"]) && $_REQUEST["directory_validate_urls"]=="on") {
    $tikilib->set_preference('directory_validate_urls','y');
    $smarty->assign('directory_validate_urls','y');
  } else {
    $tikilib->set_preference('directory_validate_urls','n');
    $smarty->assign('directory_validate_urls','n');
  }
  $tikilib->set_preference('directory_columns',$_REQUEST["directory_columns"]);
  $tikilib->set_preference('directory_links_per_page',$_REQUEST["directory_links_per_page"]);
  $tikilib->set_preference('directory_open_links',$_REQUEST["directory_open_links"]);
  $smarty->assign('directory_columns',$_REQUEST['directory_columns']);
  $smarty->assign('directory_links_per_page',$_REQUEST['directory_links_per_page']);
  $smarty->assign('directory_open_links',$_REQUEST['directory_open_links']);
}

?>
