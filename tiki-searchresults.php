<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-searchresults.php,v 1.39.2.3 2008-03-05 22:33:29 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');
require_once('lib/ajax/ajaxlib.php');

require_once ('lib/searchlib.php'); 
$auto_query_args = array('initial','maxRecords','sort_mode','find','lang','highlight','where', 'words');
$smarty->assign('headtitle',tra('Search results'));

$searchlib = &new SearchLib($tikilib->db);

if ($prefs['feature_search'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_search");

	$smarty->display("error.tpl");
	die;
}
if ($tiki_p_search != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg',tra('Permission denied'));
	$smarty->display('error.tpl');
	die;
}
if(isset($_REQUEST["highlight"]) && !empty($_REQUEST["highlight"])) {
	$_REQUEST["words"] = $_REQUEST["highlight"];
}

if ($prefs['feature_search_stats'] == 'y') {
	$searchlib->register_search(isset($_REQUEST["words"]) ? $_REQUEST["words"] : '');
}

if (!isset($_REQUEST["where"])) {
	$where = 'pages';
} else {
	$where = $_REQUEST["where"];
}
$where2 = tra($where);

$find_where='find_'.$where;
$smarty->assign('where',$where);
$filter = array();

if($where=='wikis') {
  if ($prefs['feature_wiki'] != 'y') {
    $smarty->assign('msg', tra("This feature is disabled").": feature_wiki");
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
    $smarty->assign('errortype', 401);
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
		$smarty->assign('errortype', 401);
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
	  $smarty->assign('errortype', 401);
		$smarty->assign('msg',tra("You do not have permission to use this feature"));
		$smarty->display("error.tpl");
	  die;
  }
  if (!empty($_REQUEST['forumId'])) {
	$filter['forumId'] = $_REQUEST['forumId'];
	global $commentslib; include('lib/commentslib.php');
	if (!isset($commentslib)) $commentslib = new Comments($dbTiki);
	$forum_info = $commentslib->get_forum($_REQUEST['forumId']);
	$where2 = tra('forum');
	$smarty->assign_by_ref('where3', $forum_info['name']);
	$smarty->assign_by_ref('forumId', $_REQUEST['forumId']);
	$cant_results = '';
  }
}

if($where=='files') {
	if ($prefs['feature_file_galleries'] !='y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_file_galleries");
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
}

if (($where=='galleries' || $where=='images')) {
	if ($prefs['feature_galleries'] != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_galleries");
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
}
if(($where=='trackers')) {
	if ($prefs['feature_trackers'] != 'y') {
	  $smarty->assign('msg', tra("This feature is disabled").": feature_trackers");
	  $smarty->display("error.tpl");
	  die;
	}
}
	if (isset($_REQUEST['maxRecords'])) {
		$maxRecords = $_REQUEST['maxRecords'];
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
	$results = array('cant'=>0);

	$smarty->assign('words', '');
} else {
	$words = strip_tags($_REQUEST["words"]);
	$results = $searchlib->$find_where($words, $offset, $maxRecords, $fulltext, $filter);

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

$cant_pages = ceil($results["cant"]);
$smarty->assign_by_ref('cant_results', $results["cant"]);
$smarty->assign_by_ref('cant_pages', $cant_pages);
$smarty->assign('actual_page', 1 + ($offset / $maxRecords));


$smarty->assign_by_ref('where2',$where2);

$cant=$results['cant'];
$smarty->assign('cant', $cant);

// Find search results (build array)
$smarty->assign_by_ref('results', $results["data"]);

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

ask_ticket('searchresults');

// Display the template
$smarty->assign('mid', 'tiki-searchresults.tpl');
// $smarty->assign('searchNoResults', 'true');       // false is default
// $smarty->assign('searchStyle', 'menu');           // buttons is default
// $smarty->assign('searchOrientation', 'horiz');    // vert is default 
$smarty->display("tiki.tpl");

?>
