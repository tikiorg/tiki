<?php

if(isset($_REQUEST["filegalset"])) {
  $tikilib->set_preference("home_file_gallery",$_REQUEST["homeFileGallery"]);
  $smarty->assign('home_file_gallery',$_REQUEST["homeFileGallery"]);
}

if(isset($_REQUEST["filegalfeatures"])) {
      
  if(isset($_REQUEST["feature_file_galleries_rankings"]) && $_REQUEST["feature_file_galleries_rankings"]=="on") {
    $tikilib->set_preference("feature_file_galleries_rankings",'y'); 
    $smarty->assign("feature_file_galleries_rankings",'y');
  } else {
    $tikilib->set_preference("feature_file_galleries_rankings",'n');
    $smarty->assign("feature_file_galleries_rankings",'n');
  }

  $tikilib->set_preference("fgal_match_regex",$_REQUEST["fgal_match_regex"]);
  $smarty->assign('fgal_match_regex',$_REQUEST["fgal_match_regex"]);
  $tikilib->set_preference("fgal_nmatch_regex",$_REQUEST["fgal_nmatch_regex"]);
  $smarty->assign('fgal_nmatch_regex',$_REQUEST["fgal_nmatch_regex"]);
  
  $tikilib->set_preference("fgal_use_db",$_REQUEST["fgal_use_db"]);
  $smarty->assign('fgal_use_db',$_REQUEST["fgal_use_db"]);
  $tikilib->set_preference("fgal_use_dir",$_REQUEST["fgal_use_dir"]);
  $smarty->assign('fgal_use_dir',$_REQUEST["fgal_use_dir"]);
  
  if(isset($_REQUEST["feature_file_galleries_comments"]) && $_REQUEST["feature_file_galleries_comments"]=="on") {
    $tikilib->set_preference("feature_file_galleries_comments",'y'); 
    $smarty->assign("feature_file_galleries_comments",'y');
  } else {
    $tikilib->set_preference("feature_file_galleries_comments",'n');
    $smarty->assign("feature_file_galleries_comments",'n');
  }
}

if(isset($_REQUEST["filegallistprefs"])) {
  if(isset($_REQUEST["fgal_list_name"])) {
    $tikilib->set_preference("fgal_list_name",'y');
    $smarty->assign('fgal_list_name','y');
  } else {
    $tikilib->set_preference("fgal_list_name",'n');
    $smarty->assign('fgal_list_name','n');
  }
  if(isset($_REQUEST["fgal_list_description"])) {
    $tikilib->set_preference("fgal_list_description",'y');
    $smarty->assign('fgal_list_description','y');
  } else {
    $tikilib->set_preference("fgal_list_description",'n');
    $smarty->assign('fgal_list_description','n');
  }
  if(isset($_REQUEST["fgal_list_created"])) {
    $tikilib->set_preference("fgal_list_created",'y');
    $smarty->assign('fgal_list_created','y');
  } else {
    $tikilib->set_preference("fgal_list_created",'n');
    $smarty->assign('fgal_list_created','n');
  }
  if(isset($_REQUEST["fgal_list_lastmodif"])) {
    $tikilib->set_preference("fgal_list_lastmodif",'y');
    $smarty->assign('fgal_list_lastmodif','y');
  } else {
    $tikilib->set_preference("fgal_list_lastmodif",'n');
    $smarty->assign('fgal_list_lastmodif','n');
  }
  if(isset($_REQUEST["fgal_list_user"])) {
    $tikilib->set_preference("fgal_list_user",'y');
    $smarty->assign('fgal_list_user','y');
  } else {
    $tikilib->set_preference("fgal_list_user",'n');
    $smarty->assign('fgal_list_user','n');
  }
  if(isset($_REQUEST["fgal_list_files"])) {
    $tikilib->set_preference("fgal_list_files",'y');
    $smarty->assign('fgal_list_files','y');
  } else {
    $tikilib->set_preference("fgal_list_files",'n');
    $smarty->assign('fgal_list_files','n');
  }
  if(isset($_REQUEST["fgal_list_hits"])) {
    $tikilib->set_preference("fgal_list_hits",'y');
    $smarty->assign('fgal_list_hits','y');
  } else {
    $tikilib->set_preference("fgal_list_hits",'n');
    $smarty->assign('fgal_list_hits','n');
  }
}

if(isset($_REQUEST["filegalcomprefs"])) {
  if(isset($_REQUEST["file_galleries_comments_per_page"])) {
    $tikilib->set_preference("file_galleries_comments_per_page",$_REQUEST["file_galleries_comments_per_page"]);
    $smarty->assign('file_galleries_comments_per_page',$_REQUEST["file_galleries_comments_per_page"]);
  }
  if(isset($_REQUEST["file_galleries_comments_default_ordering"])) {
    $tikilib->set_preference("file_galleries_comments_default_ordering",$_REQUEST["file_galleries_comments_default_ordering"]);
    $smarty->assign('file_galleries_comments_default_ordering',$_REQUEST["file_galleries_comments_default_ordering"]);
  }
}

$file_galleries = $tikilib->list_visible_file_galleries(0, -1, 'name_desc', 'admin','');
$smarty->assign_by_ref('file_galleries',$file_galleries["data"]);

$smarty->assign( "fgal_match_regex",
                 $tikilib->get_preference( "fgal_match_regex", '' ) );

?>
