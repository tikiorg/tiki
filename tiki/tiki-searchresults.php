<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-searchresults.php,v 1.17 2003-08-07 04:33:57 rossta Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

require_once ('lib/searchlib.php');

$searchlib = &new SearchLib($tikilib->db);

if ($feature_search != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($feature_search_stats == 'y') {
	$searchlib->register_search(isset($_REQUEST["words"]) ? $_REQUEST["words"] : '');
}

if (!isset($_REQUEST["where"])) {
	$where = 'pages';
} else {
	$where = $_REQUEST["where"];
}

$find_where = 'find_' . $where;
$smarty->assign('where', $where);
$smarty->assign('where2', tra($where));

if ($where == 'wikis' and $feature_wiki != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($where == 'directory' and $feature_directory != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($where == 'faqs' and $feature_faqs != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($where == 'forums' and $feature_forums != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($where == 'files' and $feature_file_galleries != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if ($where == 'articles' and $feature_articles != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (($where == 'galleries' || $where == 'images') and $feature_galleries != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}

if (($where == 'blogs' || $where == 'posts') and $feature_blogs != 'y') {
	$smarty->assign('msg', tra("This feature is disabled"));

	$smarty->display("styles/$style_base/error.tpl");
	die;
}
// Already assigned above! $smarty->assign('where',$where);
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}

$smarty->assign_by_ref('offset', $offset);

$fulltext = $feature_search_fulltext == 'y';

// Build the query using words
if ((!isset($_REQUEST["words"])) || (empty($_REQUEST["words"]))) {
	$results = $searchlib->$find_where(' ', $offset, $maxRecords, $fulltext);

	$smarty->assign('words', '');
} else {
	$results = $searchlib->$find_where($_REQUEST["words"], $offset, $maxRecords, $fulltext);

	$smarty->assign('words', $_REQUEST["words"]);
}

if ($fulltext == 'y') {
	$CurrentIndex = -1;

	$CurrentData = NULL;

	foreach ($results["data"] as $current) {
		if ($current["relevance"] > 0) {
			$CurrentData[++$CurrentIndex] = $current;
		}
	}

	$results['data'] = $CurrentData;
	$results['cant'] = $CurrentIndex + 1;
}

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

// Find search results (build array)
$smarty->assign_by_ref('results', $results["data"]);

// Display the template
$smarty->assign('mid', 'tiki-searchresults.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>