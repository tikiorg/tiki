<?php
// Initialization
require_once('tiki-setup.php');

if($feature_search != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($feature_search_stats == 'y') {
  $tikilib->register_search(isset($_REQUEST["words"]) ? $_REQUEST["words"] : '');
}

if(!isset($_REQUEST["where"])) {
  $where = 'pages';
} else {
  $where = $_REQUEST["where"];
}
$find_where='find_'.$where;
$smarty->assign('where',$where);

if($where=='pages' and $feature_wiki != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
if($where=='faqs' and $feature_faqs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if($where=='forums' and $feature_forums != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
if($where=='files' and $feature_file_galleries !='y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
if($where=='articles' and $feature_articles != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
if(($where=='galleries' || $where=='images') and $feature_galleries != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
if(($where=='blogs' || $where=='posts') and $feature_blogs != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}
$smarty->assign('where',$where);


if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

$fulltext = $feature_search_fulltext == 'y';

// Build the query using words
if( (!isset($_REQUEST["words"])) || (empty($_REQUEST["words"])) ) {
  $results = $tikilib->$find_where(' ',$offset,$maxRecords,$fulltext);
  $smarty->assign('words','');
} else {
  $results = $tikilib->$find_where($_REQUEST["words"],$offset,$maxRecords,$fulltext);
  $smarty->assign('words',$_REQUEST["words"]);
}

$cant_pages = ceil($results["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_results',$results["cant"]);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($results["cant"] > ($offset+$maxRecords)) {
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



// Find search results (build array)
$smarty->assign_by_ref('results',$results["data"]);

// Display the template
$smarty->assign('mid','tiki-searchresults.tpl');
$smarty->display('tiki.tpl');
?>
