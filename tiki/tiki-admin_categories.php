<?php
// Initialization
require_once('tiki-setup.php');

if($feature_categories != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($tiki_p_admin_categories != 'y') {
  $smarty->assign('msg',tra("You dont have permission to use this feature"));
  $smarty->display('error.tpl');
  die;
}


// Check for parent category or set to 0 if not present
if(!isset($_REQUEST["parentId"])) {
  $_REQUEST["parentId"]=0;
}
$smarty->assign('parentId',$_REQUEST["parentId"]);

if(isset($_REQUEST["addpage"])) {
  // Here we categorize a page
  $tikilib->categorize_page($_REQUEST["pageName"],$_REQUEST["parentId"]);
}
if(isset($_REQUEST["addpoll"])) {
  // Here we categorize a page
  $tikilib->categorize_poll($_REQUEST["pollId"],$_REQUEST["parentId"]);
}
if(isset($_REQUEST["addfaq"])) {
  // Here we categorize a page
  $tikilib->categorize_faq($_REQUEST["faqId"],$_REQUEST["parentId"]);
}
if(isset($_REQUEST["addforum"])) {
  // Here we categorize a page
  $tikilib->categorize_forum($_REQUEST["forumId"],$_REQUEST["parentId"]);
}
if(isset($_REQUEST["addgallery"])) {
  // Here we categorize a page
  $tikilib->categorize_gallery($_REQUEST["galleryId"],$_REQUEST["parentId"]);
}
if(isset($_REQUEST["addfilegallery"])) {
  // Here we categorize a page
  $tikilib->categorize_file_gallery($_REQUEST["file_galleryId"],$_REQUEST["parentId"]);
}
if(isset($_REQUEST["addarticle"])) {
  // Here we categorize a page
  $tikilib->categorize_article($_REQUEST["articleId"],$_REQUEST["parentId"]);
}
if(isset($_REQUEST["addblog"])) {
  // Here we categorize a page
  $tikilib->categorize_blog($_REQUEST["blogId"],$_REQUEST["parentId"]);
}


if(isset($_REQUEST["categId"])) {
  $info = $tikilib->get_category($_REQUEST["categId"]);
} else {
  $_REQUEST["categId"] = 0;
  $info["name"] = '';
  $info["description"] = '';
}

if(isset($_REQUEST["removeObject"])) {
  $tikilib->remove_object_from_category($_REQUEST["removeObject"],$_REQUEST["parentId"]);
}

if(isset($_REQUEST["removeCat"])) {
  $tikilib->remove_category($_REQUEST["removeCat"]);
}

if(isset($_REQUEST["save"])) {
  // Save
  if($_REQUEST["categId"]) {
    $tikilib->update_category($_REQUEST["categId"],$_REQUEST["name"],$_REQUEST["description"]);
  } else {
    $tikilib->add_category($_REQUEST["parentId"],$_REQUEST["name"],$_REQUEST["description"]);
  }
  $info["name"]='';
  $info["description"]='';
  $_REQUEST["categId"]=0;
}
$smarty->assign('categId',$_REQUEST["categId"]);
$smarty->assign('name',$info["name"]);
$smarty->assign('description',$info["description"]);


// If the parent category is not zero get the category path
if($_REQUEST["parentId"]) {
  $path = $tikilib->get_category_path_Admin($_REQUEST["parentId"]);
  $p_info = $tikilib->get_category($_REQUEST["parentId"]);
  $father = $p_info["parentId"];
} else {
  $path = "TOP";
  $father = 0;
}
$smarty->assign('path',$path);
$smarty->assign('father',$father);

$children = $tikilib->get_child_categories($_REQUEST["parentId"]);
$smarty->assign_by_ref('children',$children);

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'name_asc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 
if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

if(isset($_REQUEST["find_objects"])) {
  $find_objects=$_REQUEST["find_objects"];
} else {
  $find_objects='';
}
$smarty->assign('find_objects',$find_objects);

$smarty->assign_by_ref('sort_mode',$sort_mode);
$smarty->assign_by_ref('find',$find);
$objects = $tikilib->list_category_objects($_REQUEST["parentId"],$offset,$maxRecords,$sort_mode,$find);
$smarty->assign_by_ref('objects',$objects["data"]);

$cant_pages = ceil($objects["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($objects["cant"] > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}

$galleries = $tikilib->list_galleries(0, -1, 'name_desc', 'admin', $find_objects) ;
$smarty->assign_by_ref('galleries',$galleries["data"]);

$file_galleries = $tikilib->list_file_galleries(0, -1, 'name_desc', 'admin', $find_objects) ;
$smarty->assign_by_ref('file_galleries',$file_galleries["data"]);

$forums = $tikilib->list_forums(0,-1,'name_asc',$find_objects);
$smarty->assign_by_ref('forums',$forums["data"]);

$polls = $tikilib->list_polls(0,-1,'title_asc',$find_objects);
$smarty->assign_by_ref('polls',$polls["data"]);

$blogs = $tikilib->list_blogs(0,-1,'title_asc',$find_objects);
$smarty->assign_by_ref('blogs',$blogs["data"]);

$pages = $tikilib->list_pages(0, -1,  'pageName_asc', $find_objects);
$smarty->assign_by_ref('pages',$pages["data"]);

$faqs = $tikilib->list_faqs(0, -1,  'title_asc', $find_objects);
$smarty->assign_by_ref('faqs',$faqs["data"]);


$articles = $tikilib->list_articles(0,-1,'title_asc', $find_objects, '',$user);
$smarty->assign_by_ref('articles',$articles["data"]);

// Display the template
$smarty->assign('mid','tiki-admin_categories.tpl');
$smarty->display('tiki.tpl');
?>