<?php
$setup_parsed_uri = parse_url($_SERVER["REQUEST_URI"]);
if(isset($setup_parsed_uri["query"])) {
  parse_str($setup_parsed_uri["query"],$setup_query_data);
} else {
  $setup_query_data = Array();	
}

if ($user_assigned_modules=='y' && $tiki_p_configure_modules=='y') {
// check the session to get the parent or create parent =0
$smarty->assign('ownurl','http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
if(!isset($_SESSION["bookmarks_parent"])) {
  $_SESSION["bookmarks_parent"]=0;
}
if(isset($_REQUEST["bookmarks_parent"])) {
  $_SESSION["bookmarks_parent"]=$_REQUEST["bookmarks_parent"];
}

$ownurl = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
// Now build urls
if(strstr($ownurl,'?')) {
  $modb_sep='&amp;';
} else {
  $modb_sep='?';
}
$smarty->assign('modb_sep',$modb_sep);

if(isset($_REQUEST["bookmark_removeurl"])) {
  $tikilib->remove_url($_REQUEST["bookmark_removeurl"],$user);
}

if(isset($_REQUEST["bookmark_create_folder"])) {
  $tikilib->add_folder($_SESSION["bookmarks_parent"],$bookmark_urlname,$user);
}

if(isset($_REQUEST["bookmark_mark"])) {
  if(empty($_REQUEST["bookmark_urlname"])) {
    // Check if we are bookmarking a wiki-page	
    if(strstr($_SERVER["REQUEST_URI"],'tiki-index')) {
      // Get the page
      if(isset($setup_query_data["page"])) {
        $_REQUEST["bookmark_urlname"] = $setup_query_data["page"];	
      } else {
      	$_REQUEST["bookmark_urlname"] = 'HomePage';
      }
    }
    // Check if we are bookmarking an article
    // Check if we are bookmarking an image gallery
    // Check if we are bookmarking a file gallery
    // Check if we are bookmarking a forum
    // Check if we are bookmarking a forum topic
    // Check if we are bookmarking a faq
    // Check if we are bookmarking a poll-results page
    // Check if we are bookmarking a weblog
    
  }
  if(!empty($_REQUEST["bookmark_urlname"])) {
    $tikilib->replace_url(0,$_SESSION["bookmarks_parent"],$_REQUEST["bookmark_urlname"],$ownurl,$user);
  }
}

$modb_p_info = $tikilib->get_folder($_SESSION["bookmarks_parent"],$user);
$modb_father = $modb_p_info["parentId"];
// get folders for the parent
$modb_urls = $tikilib->list_folder($_SESSION["bookmarks_parent"],0,-1,'name_asc','',$user);
$smarty->assign('modb_urls',$modb_urls["data"]);
$modb_folders = $tikilib->get_child_folders($_SESSION["bookmarks_parent"],$user);
$modb_pf = Array(
  "name" => "..",
  "folderId" => $modb_father,
  "parentId" => 0,
  "user" => $user
);
$modb_pfs =Array($modb_pf);
if($_SESSION["bookmarks_parent"]) {
  $modb_folders = array_merge($modb_pfs,$modb_folders);
}
$smarty->assign('modb_folders',$modb_folders);
// get urls for the parent
}
?>