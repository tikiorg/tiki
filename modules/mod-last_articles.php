<?php
// $Id$

// Copyright (c) 2002-2009, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

/**
 * Show recent articles.
 *
 **/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (!function_exists('mod_last_articles_help')) {
	function mod_last_articles_help() {
		return "type=Article|Event|...&topicId=1&topic=xx&categId=1&lang=en&showImg=width&showDate=y&showHeading=chars";
	}
}
global $tikilib, $smarty;
// Parameter absurl set if the last_article url is absolute or not [y|n].
// If not set, default = relative

$urlParams = array(
	'topicId' => 'topic',
	'topic' => 'topicName',
	'categId' => 'categId',
	'type' => 'type',
	'lang' => 'lang',
	'showImg' => NULL,
	'showDate' => NULL,
	'showHeading' => NULL,
	'nonums' => NULL,
	'absurl' => NULL
);

foreach ( $urlParams as $p => $v ) {
	if ( isset($$p) ) continue;
	$$p = isset($module_params[$p]) ? $module_params[$p] : '';
}

$offset = (int) $module_params['offset'];
if ( $absurl == '' ) $absurl = 'n';
if ( $nonums == '' ) $nonums = 'n';
if ( $showHeading != 'n') {
	if ( $showHeading == 'y' ) $showHeading = -1;
	foreach ( $ranking['data'] as $key => $article ) {
		$ranking['data'][$key]['parsedHeading'] = $tikilib->parse_data($article['heading']);
	}
}

foreach ( $urlParams as $p => $v ) $smarty->assign($p, $$p);

$ranking = $tikilib->list_articles($offset, $module_rows, 'publishDate_desc', '', '', '', false, $type, $topicId, 'y', $topic, $categId, '', '', $lang);


$module_rows = count($ranking['data']);
$smarty->assign('module_rows', $module_rows);
$smarty->assign('modLastArticles', $ranking['data']);
