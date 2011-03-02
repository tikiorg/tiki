<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
define('PATTERN_TO_CLEAN_TEXT', '/[^0-9a-zA-Z_]/');
define('CLEAN_CHAR', '-');
define('TITLE_SEPARATOR', '-');
function filter_out_sefurl($tpl_output, &$smarty, $type = null, $title = '', $with_next = null, $with_title='y') {
	global $sefurl_regex_out, $tikilib, $prefs, $base_url;
	if ($prefs['feature_sefurl'] != 'y' or ( preg_match('#^http(|s)://#',$tpl_output) and strpos($tpl_output, $base_url) !== 0 ) ) {
		return $tpl_output;
	}
	global $cachelib;
	include_once ('lib/cache/cachelib.php');
	if (!is_array($sefurl_regex_out)) {
		if (! $sefurl_regex_out = $cachelib->getSerialized('sefurl_regex_out')) {
			$query = 'select * from `tiki_sefurl_regex_out` where `silent` != ? order by `order` asc';
			$result = $tikilib->query($query, array('y'));
			$sefurl_regex_out = array();
			if (!empty($result)) {
				while ($res = $result->fetchRow()) {
					if (empty($res['feature']) || $prefs[$res['feature']] == 'y') {
						$sefurl_regex_out[] = $res;
					}
				}
			}
			$cachelib->cacheItem('sefurl_regex_out', serialize($sefurl_regex_out));
		}
	}
	if ($type == 'article' && $prefs['feature_sefurl_title_article'] == 'y' && empty($with_next) && $with_title == 'y') {
		global $artlib;
		include_once ('lib/articles/artlib.php');
		if (preg_match('/articleId=([0-9]+)/', $tpl_output, $matches)) {
			if (empty($title)) $title = $artlib->get_title($matches[1]);
			$title = preg_replace(PATTERN_TO_CLEAN_TEXT, CLEAN_CHAR, $tikilib->take_away_accent($title));
			$title = preg_replace('/' . CLEAN_CHAR . CLEAN_CHAR . '+/', '-', $title);
			$title = preg_replace('/' . CLEAN_CHAR . '+$/', '', $title);
		}
	}
	if ($type == 'blog' && $prefs['feature_sefurl_title_blog'] == 'y' && empty($with_next) && $with_title == 'y') {
		global $bloglib;
		include_once ('lib/blogs/bloglib.php');
		if (preg_match('/blogId=([0-9]+)/', $tpl_output, $matches)) {
			if (empty($title)) $title = $bloglib->get_title($matches[1]);
			$title = preg_replace(PATTERN_TO_CLEAN_TEXT, CLEAN_CHAR, $tikilib->take_away_accent($title));
			$title = preg_replace('/' . CLEAN_CHAR . CLEAN_CHAR . '+/', '-', $title);
			$title = preg_replace('/' . CLEAN_CHAR . '+$/', '', $title);
		}
	}
	if ($type == 'blogpost' && $prefs['feature_sefurl_title_blog'] == 'y' && empty($with_next) && $with_title == 'y') {
		global $bloglib;
		include_once ('lib/blogs/bloglib.php');
		if (preg_match('/postId=([0-9]+)/', $tpl_output, $matches)) {
			if (empty($title)) {
				if ($post_info = $bloglib->get_post($matches[1])) $title = $post_info['title'];
			}
			$title = preg_replace(PATTERN_TO_CLEAN_TEXT, CLEAN_CHAR, $tikilib->take_away_accent($title));
			$title = preg_replace('/' . CLEAN_CHAR . CLEAN_CHAR . '+/', '-', $title);
			$title = preg_replace('/' . CLEAN_CHAR . '+$/', '', $title);
		}
	}
	if ($type == 'tracker item' || $type == 'trackeritem') {
		if (preg_match('/itemId=([0-9]+)/', $tpl_output, $matches)) {
			$trklib = TikiLib::lib('trk');
			if ($prefs["feature_sefurl_tracker_prefixalias"] == 'y' && $pagealias = $trklib->get_trackeritem_pagealias($matches[1])){
				$tpl_output = "./tiki-index.php?page=" . $pagealias;
			}
		}
	}
	if ($type == 'category' && !empty($title)) {
		$title = preg_replace(PATTERN_TO_CLEAN_TEXT, CLEAN_CHAR, $tikilib->take_away_accent($title));
		$title = preg_replace('/' . CLEAN_CHAR . CLEAN_CHAR . '+/', '-', $title);
		$title = preg_replace('/' . CLEAN_CHAR . '+$/', '', $title);	
	}
	foreach($sefurl_regex_out as $regex) {
		if (empty($type) || $type == $regex['type']) {
			// if a question mark in pattern, deal with possible additional terms
			// The '?&' isn't pretty but seems to work.
			//if( strpos($regex['left'],'?') !== FALSE ) {
			//	$tpl_output = preg_replace( '/'.$regex['left'].'&/', $regex['right'].'?&', $tpl_output );
			//}
			$tpl_output = preg_replace('/' . $regex['left'] . '/', $regex['right'], $tpl_output);
		}
	}
	if (!empty($title)) {
		$tpl_output.= TITLE_SEPARATOR . $title;
	}
	if (is_array($prefs['feature_sefurl_paths'])) {
		foreach($prefs['feature_sefurl_paths'] as $path) {
			if (isset($_REQUEST[$path])) {
				$tpl_output = urlencode($_REQUEST[$path]) . "/$tpl_output";
			}
		}
	}
	return $tpl_output;
}
