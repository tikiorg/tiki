<?php
// Initialization
require_once('tiki-setup.php');

if($feature_blogs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


if(!isset($_REQUEST["blogId"])) {
  $smarty->assign('msg',tra("No blog indicated"));
  $smarty->display('error.tpl');
  die;  
}

$blog_data = $tikilib->get_blog($_REQUEST["blogId"]);
if(!$blog_data) {
  $smarty->assign('msg',tra("Blog not found"));
  $smarty->display('error.tpl');
  die;  
}
$tikilib->add_blog_hit($_REQUEST["blogId"]);
$smarty->assign('blogId',$_REQUEST["blogId"]);
$smarty->assign('title',$blog_data["title"]);
$smarty->assign('description',$blog_data["description"]);
$smarty->assign('created',$blog_data["created"]);
$smarty->assign('lastModif',$blog_data["lastModif"]);
$smarty->assign('posts',$blog_data["posts"]);
$smarty->assign('public',$blog_data["public"]);
$smarty->assign('hits',$blog_data["hits"]);
$smarty->assign('creator',$blog_data["user"]);
$smarty->assign('activity',$blog_data["activity"]);

if(isset($_REQUEST["remove"])) {
  $data = $tikilib->get_post($_REQUEST["remove"]);
  if($data["user"]!=$user) {
    if($tiki_p_blog_admin != 'y') {
      $smarty->assign('msg',tra("Permission denied you cannot remove the post"));
      $smarty->display('error.tpl');
      die;  
    }
  }
  $tikilib->remove_post($_REQUEST["remove"]);  
}


// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'created_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 


$smarty->assign_by_ref('sort_mode',$sort_mode);

// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

$now = date("U");
if(isset($_SESSION["thedate"])) {
  if($_SESSION["thedate"]<$now) {
    $pdate = $_SESSION["thedate"]; 
  } else {
    if($tiki_p_blog_admin == 'y') {
      $pdate = $_SESSION["thedate"]; 
    } else {
      $pdate = $now;
    }
  }
} else {
  $pdate = $now;
}

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}

// Get a list of last changes to the Wiki database
$listpages = $tikilib->list_blog_posts($_REQUEST["blogId"], $offset, $blog_data["maxPosts"], $sort_mode, $find, $pdate);

for($i=0;$i<count($listpages["data"]);$i++) {
  $listpages["data"][$i]["parsed_data"] = $tikilib->parse_data($listpages["data"][$i]["data"]);
}
$maxRecords=$blog_data["maxPosts"];
$cant_pages = ceil($listpages["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));

if($listpages["cant"] > ($offset + $maxRecords)) {
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


// If there're more records then assign next_offset
$smarty->assign_by_ref('listpages',$listpages["data"]);
//print_r($listpages["data"]);

// Display the template
$smarty->assign('mid','tiki-view_blog.tpl');
$smarty->display('tiki.tpl');
?>
