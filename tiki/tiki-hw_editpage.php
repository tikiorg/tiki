<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-hw_editpage.php,v 1.1 2004-02-05 05:19:00 ggeller Exp $

// Copyright (c) 2004 George G. Geller
// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Adapted from tiki-editpage.php

// Students cannot edit/view another student's page.  This may change in VERSION2 when we do peer reviewers.
// Admin, teachers and graders can edit anyone's page.

error_reporting (E_ALL);

// Initialization
require_once ('tiki-setup.php');
include_once ('lib/wiki/wikilib.php');

include_once("lib/homework/homeworklib.php");
$homeworklib = new HomeworkLib($dbTiki);

if ($feature_homework != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_homework");
  $smarty->display("error.tpl");
  die;
}

if ($tiki_p_hw_student != 'y') {
  $smarty->assign('msg', tra("You must be a student to edit homework pages."));
  $smarty->display("error.tpl");
  die;
}

if (!isset($_REQUEST["id"])) {
  $smarty->assign('msg', tra("No assignment indicated"));
  $smarty->display("error.tpl");
  die;
}
$pageId = $_REQUEST["id"];

// Fetch the page try to lock it.
$status = $homeworklib->hw_page_fetch_by_id(&$page_data, $pageId, true);
if ($status == "HW_INVALID_ID"){
  $smarty->assign('msg', tra("Error: Invalid id for hw_pages table."));
  $smarty->display("error.tpl");
  die;
} elseif ($status == "HW_PAGE_LOCKED"){
  $smarty->assign('msg', tra("Error: This page is being edited by another user."));
  $smarty->display("error.tpl");
  die;
}
$ggg_tracer->out(__FILE__." line ".__LINE__.' $page_data = ');
$ggg_tracer->outvar($page_data);

// VERSION2 - change this for peer review
$studentName = $page_data['studentName'];
if (!$tiki_p_hw_grader && $usr != $studentName /* && !homeworklib->hw_peer_review_permit($user, $pageID) */){
  $smarty->assign('msg', tra("Permission denied: Students may only view or edit their own work."));
  $smarty->display("error.tpl");
  die;
}

$assignmentId = $page_data['assignmentId'];
$smarty->assign('assignmentId',$assignmentId);

$assignment_data = $homeworklib->get_assignment($assignmentId);
$smarty->assign('homeworkTitle', $assignment_data["title"]);

$ggg_tracer->out(__FILE__." line ".__LINE__.' $_REQUEST = ');
$ggg_tracer->outvar($_REQUEST);

$page_data["lockedBy"] = $user;
// Need to do homework-style locking
// The page is locked if another users is editing it.
// Should make the locks expire.
if ($page_data["flag"] == 'L' && $user != $page_data["lockedBy"]) {
  $ggg_tracer->outln(__FILE__." line ".__LINE__.' It is locked?');
  $smarty->assign('msg', tra("Cannot edit page because it is locked"));
  $smarty->display("error.tpl");
  die;
}

$_REQUEST["allowhtml"] = 'off';

$smarty->assign_by_ref('data', $page_data);

// $smarty->assign('footnote', '');
// $smarty->assign('has_footnote', 'n');

/* if ($feature_wiki_footnotes == 'y') {
  $ggg_tracer->outln(__FILE__." line ".__LINE__.' Doing footnotes');
	if ($user) {
		$x = $wikilib->get_footnote($user, $page);

		$footnote = $wikilib->get_footnote($user, $page);
		$smarty->assign('footnote', $footnote);

		if ($footnote)
			$smarty->assign('has_footnote', 'y');

		$smarty->assign('parsed_footnote', $tikilib->parse_data($footnote));

		if (isset($_REQUEST['footnote'])) {
			check_ticket('edit-page');
			$smarty->assign('parsed_footnote', $tikilib->parse_data($_REQUEST['footnote']));

			$smarty->assign('footnote', $_REQUEST['footnote']);
			$smarty->assign('has_footnote', 'y');

			if (empty($_REQUEST['footnote'])) {
				$wikilib->remove_footnote($user, $page);
			} else {
				$wikilib->replace_footnote($user, $page, $_REQUEST['footnote']);
			}
		}
	}
} */

$smarty->assign('commentdata', '');

$edit_data = $page_data["data"];
$smarty->assign_by_ref('pagedata',htmldecode($edit_data));
$parsed = $tikilib->parse_data($edit_data);

/* SPELLCHECKING INITIAL ATTEMPT */
//This nice function does all the job!
if ($wiki_spellcheck == 'y') {
  $ggg_tracer->outln(__FILE__." line ".__LINE__);
  if (isset($_REQUEST["spellcheck"]) && $_REQUEST["spellcheck"] == 'on') {
    $parsed = $tikilib->spellcheckreplace($edit_data, $parsed, $language, 'editwiki');
    $smarty->assign('spellcheck', 'y');
  } else {
    $smarty->assign('spellcheck', 'n');
  }
}

$smarty->assign_by_ref('parsed', $parsed);

$smarty->assign('preview',0);
// If we are in preview mode then preview it!
if(isset($_REQUEST["preview"])) {
  $ggg_tracer->outln(__FILE__." line ".__LINE__);
  $smarty->assign('preview',1); 
}

function htmldecode($string) {
  global $ggg_tracer;
  $ggg_tracer->outln(__FILE__." line ".__LINE__);
  $string = strtr($string, array_flip(get_html_translation_table(HTML_ENTITIES)));
  $string = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $string);
  return $string;
}

function parse_output(&$obj, &$parts,$i) {  
  $ggg_tracer->outln(__FILE__." line ".__LINE__);
  if(!empty($obj->parts)) {    
    for($i=0; $i<count($obj->parts); $i++)      
      parse_output($obj->parts[$i], $parts,$i);  
  }else{    
    $ctype = $obj->ctype_primary.'/'.$obj->ctype_secondary;    
    switch($ctype) {    
    case 'application/x-tikiwiki':
      $aux["body"] = $obj->body;  
      $ccc=$obj->headers["content-type"];
      $items = split(';',$ccc);
      foreach($items as $item) {
	$portions = split('=',$item);
	if(isset($portions[0])&&isset($portions[1])) {
	  $aux[trim($portions[0])]=trim($portions[1]);
	}
      }
      
      
      $parts[]=$aux;
      
    }  
  }
}

// Pro
// Check if the page has changed
if (isset($_REQUEST["save"])) {
  // save the page
  $ggg_tracer->outln(__FILE__." line ".__LINE__." Saving a page!");
  $ggg_tracer->out(__FILE__." line ".__LINE__.' $_REQUEST = ');
  $ggg_tracer->outvar($_REQUEST);
  check_ticket('edit-page');

  include_once("lib/imagegals/imagegallib.php");
  $cat_type='wiki page';
  $cat_objid = $_REQUEST["page"];

  $cat_name = $_REQUEST["page"];
  $cat_href="tiki-index.php?page=".$cat_objid;
  include_once("categorize.php");
  
  if ((md5($info["data"]) != md5($_REQUEST["edit"]))) {
    $ggg_tracer->outln(__FILE__." line ".__LINE__." The data changed!");
    
    $page = "HomePage"; // GGG scaffolding
    
	$edit = htmlspecialchars($_REQUEST['edit']);
    
    // add permisions here otherwise return error!
    if(isset($wiki_feature_copyrights) && $wiki_feature_copyrights == 'y'
       && isset($_REQUEST['copyrightTitle'])
       && isset($_REQUEST['copyrightYear'])
       && isset($_REQUEST['copyrightAuthors'])
       && !empty($_REQUEST['copyrightYear'])
       && !empty($_REQUEST['copyrightTitle'])
       ) {
      include_once("lib/copyrights/copyrightslib.php");
      $copyrightslib = new CopyrightsLib($dbTiki);
      $copyrightYear = $_REQUEST['copyrightYear'];
      $copyrightTitle = $_REQUEST['copyrightTitle'];
      $copyrightAuthors = $_REQUEST['copyrightAuthors'];
      $copyrightslib->add_copyright($page,$copyrightTitle,$copyrightYear,$copyrightAuthors,$user);
    }

    // Parse $edit and eliminate image references to external URIs (make them internal)
    $edit = $imagegallib->capture_images($edit);
    
    // If page exists
    if(!$tikilib->page_exists($_REQUEST["page"])) {
      // Extract links and update the page
      
      $links = $tikilib->get_links($_REQUEST["edit"]);
      /*
	  $notcachedlinks = $tikilib->get_links_nocache($_REQUEST["edit"]);
	  $cachedlinks = array_diff($links, $notcachedlinks);
	  $tikilib->cache_links($cachedlinks); 
      */
      $t = date("U");
      $tikilib->create_page($_REQUEST["page"], 0, $edit, $t, $_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"],$description);  
      if ($wiki_watch_author == 'y') 
        $tikilib->add_user_watch($user,"wiki_page_changed",$_REQUEST["page"],tra('Wiki page'),$page,"tiki-index.php?page=$page");
      
    } else {
      $links = $tikilib->get_links($edit);
      /*
      $tikilib->cache_links($links);
      */
      if(isset($_REQUEST['isminor'])&&$_REQUEST['isminor']=='on') {
        $minor=true;
      } else {
        $minor=false;
      }
      $tikilib->update_page($_REQUEST["page"],$edit,$_REQUEST["comment"],$user,$_SERVER["REMOTE_ADDR"],$description,$minor);
    }
    
    $page = urlencode($page);
    if ($page_ref_id) {
      header("location: tiki-index.php?page_ref_id=$page_ref_id");
    } else {
      header("location: tiki-index.php?page=$page");
    }
    die;
  } else {
    // $ggg_tracer->outln(__FILE__." line ".__LINE__." The data didn't change!");
    $page = urlencode($page);
	header("location: tiki-hw_page.php?assignmentId=".$page_data['assignmentId']);
    die;
  }
}

// If we're creating a new version; it needs a new comment.
$_REQUEST["comment"] = '';

include_once("textareasize.php");

include_once ('lib/quicktags/quicktagslib.php');
$quicktags = $quicktagslib->list_quicktags(0,-1,'taglabel_desc','');
$smarty->assign_by_ref('quicktags', $quicktags["data"]);
ask_ticket('edit-page');

$smarty->assign("pageId",$pageId);

// Display the Index Template
$smarty->assign('mid', 'tiki-hw_editpage.tpl');
$smarty->assign('show_page_bar', 'y');
$smarty->display("tiki.tpl");

?>
