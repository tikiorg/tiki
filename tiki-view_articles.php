<?php
// Initialization
require_once('tiki-setup.php');
include_once('lib/articles/artlib.php');

/*
if($feature_listPages != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}
*/

if($feature_articles != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if($tiki_p_read_article != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}



if(isset($_REQUEST["remove"])) {
  if($tiki_p_remove_article != 'y') {
    $smarty->assign('msg',tra("Permission denied you cannot remove articles"));
    $smarty->display("styles/$style_base/error.tpl");
    die;  
  }
  $artlib->remove_article($_REQUEST["remove"]);  
}




// This script can receive the thresold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'publishDate_desc'; 
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
    if($tiki_p_admin == 'y') {
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
$listpages = $tikilib->list_articles(0,$maxArticles,$sort_mode,$find,$pdate,$user);
for($i=0;$i<count($listpages["data"]);$i++) {
  $listpages["data"][$i]["parsed_heading"] = $tikilib->parse_data($listpages["data"][$i]["heading"]);
}

// If there're more records then assign next_offset
$smarty->assign_by_ref('listpages',$listpages["data"]);
//print_r($listpages["data"]);

$section='cms';
include_once('tiki-section_options.php');

// Display the template
$smarty->assign('mid','tiki-view_articles.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>
