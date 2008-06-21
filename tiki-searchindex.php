<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-searchindex.php,v 1.16.2.4 2008-03-05 22:33:29 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/search/searchlib.php'); 
// note: lib/search/searchlib.php is new. the old one was lib/searchlib.php

$searchlib = &new SearchLib($tikilib->db);

$smarty->assign('headtitle',tra('Search results'));

if ($prefs['feature_search'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_search");

	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_search != 'y') {
	$smarty->assign('msg',tra('Permission denied'));
	$smarty->display('error.tpl');
	die;
}

if(isset($_REQUEST["highlight"]) && !empty($_REQUEST["highlight"])) {
  $_REQUEST["words"]=$_REQUEST["highlight"];
}

if ($prefs['feature_search_stats'] == 'y') {
	$searchlib->register_search(isset($_REQUEST["words"]) ? $_REQUEST["words"] : '');
}

if (!isset($_REQUEST["where"])) {
	$where = 'pages';
} else {
	$where = $_REQUEST["where"];
}

$smarty->assign('where',$where);
$smarty->assign('where2',tra($where));
$filter = array();

if($where=='wikis') {
  if ($prefs['feature_wiki'] != 'y') {
    $smarty->assign('msg', tra("This feature is disabled").": feature_wiki");
    $smarty->display("error.tpl");
    die;
  }
  if($tiki_p_admin_wiki != 'y'  && $tiki_p_view != 'y') {
    $smarty->assign('msg',tra("Permission denied you cannot view this section"));
    $smarty->display("error.tpl");
    die;  
  }
}

if($where=='directory') {
	if ($prefs['feature_directory'] != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_directory");
  $smarty->display("error.tpl");
  die;
	}
  if($tiki_p_admin_directory != 'y' && $tiki_p_view_directory != 'y') {
    $smarty->assign('msg',tra("Permission denied"));
    $smarty->display("error.tpl");
    die;  
  }
}

if($where=='faqs') {
	if ($prefs['feature_faqs'] != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_faqs");
	  $smarty->display("error.tpl");
	  die;
	}
	if($tiki_p_admin_faqs != 'y' && $tiki_p_view_faqs != 'y') {
		$smarty->assign('msg',tra("You do not have permission to use this feature"));
    $smarty->display("error.tpl");
    die;
	}
}

if($where=='forums') {
	if ($prefs['feature_forums'] != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_forums");
  	$smarty->display("error.tpl");
  	die;
	}
  if($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
		$smarty->assign('msg',tra("You do not have permission to use this feature"));
		$smarty->display("error.tpl");
	  die;
  }
  if (!empty($_REQUEST['forumId'])) {
	$filter['forumId'] = $_REQUEST['forumId'];
  }
}

if($where=='files') {
	if ($prefs['feature_file_galleries'] !='y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
	  $smarty->display("error.tpl");
	  die;
	}
	if($tiki_p_view_file_gallery != 'y') {
	  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
	  $smarty->display("error.tpl");
	  die;
	}
}

if($where=='articles') {
	if ($prefs['feature_articles'] != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_articles");
	  $smarty->display("error.tpl");
	  die;
	}
	if($tiki_p_read_article != 'y') {
	  $smarty->assign('msg',tra("Permission denied you cannot view this section"));
	  $smarty->display("error.tpl");
	  die;  
	}
}

if (($where=='galleries' || $where=='images')) {
	if ($prefs['feature_galleries'] != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_galleries");
	  $smarty->display("error.tpl");
	  die;
	}
  if($tiki_p_view_image_gallery != 'y') {
    $smarty->assign('msg',tra("Permission denied you can not view this section"));
    $smarty->display("error.tpl");
    die;  
  }
}

if(($where=='blogs' || $where=='posts')) {
	if ($prefs['feature_blogs'] != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_blogs");
	  $smarty->display("error.tpl");
	  die;
	}
	if($tiki_p_read_blog != 'y') {
	  $smarty->assign('msg',tra("Permission denied you can not view this section"));
	  $smarty->display("error.tpl");
	  die;  
	}
}

if(($where=='trackers')) {
        if ($prefs['feature_trackers'] != 'y') {
          $smarty->assign('msg', tra("This feature is disabled").": feature_trackers");
          $smarty->display("error.tpl");
          die;
        }
        if($tiki_p_view_trackers != 'y') {
          $smarty->assign('msg',tra("Permission denied you can not view this section"));
          $smarty->display("error.tpl");
          die;
        }
}

// Already assigned above! $smarty->assign('where',$where);
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

$fulltext = $prefs['feature_search_fulltext'] == 'y';

// Build the query using words
if ((!isset($_REQUEST["words"])) || (empty($_REQUEST["words"]))) {
  $results = $searchlib->find($where,' ', $offset, $maxRecords, $fulltext, $filter);

	$smarty->assign('words', '');
} else {
	$words = strip_tags($_REQUEST["words"]);
	$results = $searchlib->find($where,$words, $offset, $maxRecords, $fulltext, $filter);

	$smarty->assign('words', $words);
}

//if ($fulltext == 'y') {
//	$CurrentIndex = -1;
//	$CurrentData = NULL;
//	foreach ($results["data"] as $current) {
//		if ($current["relevance"] > 0) {
//			$CurrentData[++$CurrentIndex] = $current;
//		}
//	}
//	$results['data'] = $CurrentData;
//	$results['cant'] = $CurrentIndex + 1;
//}

$cant_pages = ceil($results["cant"] / $maxRecords);
$smarty->assign('cant_results', $results["cant"]);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));

if ($results["cant"] > ($offset + $maxRecords)) {
	$smarty->assign('next_offset', $offset + $maxRecords);
} else {
	$smarty->assign('next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($offset > 0) {
	$smarty->assign('prev_offset', $offset - $maxRecords);
} else {
	$smarty->assign('prev_offset', -1);
}

$where_list = array('pages' => 'Entire Site');
if ( $prefs['feature_wiki'] == 'y' ) {
	$where_list['wikis'] = 'Wiki Pages';
}
if ( $prefs['feature_galleries'] == 'y' ) {
	$where_list['galleries'] = 'Galleries';
	$where_list['images'] = 'Images';
}
if ( $prefs['feature_file_galleries'] == 'y' ) {
	$where_list['files'] = 'Files';
}
if ( $prefs['feature_forums'] == 'y' ) {
	$where_list['forums'] = 'Forums';
}
if ( $prefs['feature_faqs'] == 'y' ) {
	$where_list['faqs'] = 'FAQs';
}
if ( $prefs['feature_blogs'] == 'y' ) {
	$where_list['blogs'] = 'Blogs';
	$where_list['posts'] = 'Blog Posts';
}
if ( $prefs['feature_directory'] == 'y' ) {
	$where_list['directory'] = 'Directory';
}
if ( $prefs['feature_articles'] == 'y' ) {
	$where_list['articles'] = 'Articles';
}
if ( $prefs['feature_trackers'] == 'y' ) {
	$where_list['trackers'] = 'Trackers';
}
$smarty->assign_by_ref('where_list', $where_list);

// Find search results (build array)
$smarty->assign_by_ref('results', $results["data"]);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-searchindex.tpl');
// $smarty->assign('searchNoResults', 'true');       // false is default
// $smarty->assign('searchStyle', 'menu');           // buttons is default
// $smarty->assign('searchOrientation', 'horiz');    // vert is default 
$smarty->display("tiki.tpl");

?>
